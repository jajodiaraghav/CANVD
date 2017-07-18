<?php
include_once('../common.php');
include_once('../partials/header.php');
?>
<div class="container">
	<div id="content">
    <div class="jumbotron">
      <div class="container">
        <span id="title_fix"></span>
          <p id="main-top-text">
            Variant(s) of <span style="color:#ea2f10" id="gene-name"><?=$_GET['variant']?></span> protein.
          </p>
      </div>
      <?php
      if(isset($_GET['variant']))
      {
        $query = 'SELECT GeneName, Description FROM T_Ensembl WHERE EnsPID=:ens';
        $stmt = $dbh->prepare($query);
        $param = array(':ens'=> $_GET['variant']);
        $stmt->execute($param);
        while ($row = $stmt->fetch())
        {
          $name = $row[0];
          $description = $row[1];
        }

        if(!isset($name)) die('Error: Protein not found.');

        if(isset($_GET['tissues']))
        {
          $tissue = $_GET['tissues'];
          $tissues = explode(",",$tissue);
          function sanitize($s) { return htmlspecialchars($s); }
          $t = array_map('sanitize', $tissues);
          $P_List = "'" . implode("','", $t) . "'";

          $query = 'SELECT Peptide_EnsPID, Tumour_Site, Mutation_ID, Mut_Description
                    FROM T_Mutations WHERE Peptide_EnsPID=:ens AND Tumour_Site IN(' . $P_List . ')';
        }
        else
        {
          $query = 'SELECT Peptide_EnsPID, Tumour_Site, Mutation_ID, Mut_Description
                    FROM T_Mutations WHERE Peptide_EnsPID=:ens';
        }

        $stmt = $dbh->prepare($query);
        $query_params = array(':ens'=> $_GET['variant']);
        $stmt->execute($query_params);
        $variants = array();
        while ($row = $stmt->fetch())
        {
          $variants[] = $row;
        }

        // Get all effects
        $effects = array();
        foreach($variants as $var)
        {
          $query = 'SELECT Eval, Domain_EnsPID FROM T_Interactions_MT INNER JOIN T_Interactions
                    ON T_Interactions.IID = T_Interactions_MT.IID WHERE Peptide_EnsPID=:ens';
          $stmt = $dbh->prepare($query);
          $query_params = array(':ens'=> $var[0]);
          $stmt->execute($query_params);
          while ($row = $stmt->fetch())
          {
            $effects[$row[1]] = $row[0];
          }
        }

        // Get all interacting domains
        $query = 'SELECT Domain_EnsPID, Peptide_EnsPID, IID FROM T_Interactions WHERE Peptide_EnsPID=:ens';
        $stmt = $dbh->prepare($query);
        $query_params = array(':ens'=> $_GET['variant']);
        $stmt->execute($query_params);
        $DPID = array();
        while ($row = $stmt->fetch())
        {
          $query2 = 'SELECT GeneName FROM T_Ensembl WHERE EnsPID=:ens LIMIT 1';
          $hdr = $dbh->prepare($query2);
          $par = array(':ens'=> $row[0]);
          $hdr->execute($par);
          $DPID[$hdr->fetch()[0]] = $row[0];
        }
      ?>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="navbar navbar-inverse">
            <div class="navbar-header">
              <a class="navbar-brand" href="#"><?=$name?> (<?=$_GET['variant']?>)</a>
              <script type="text/javascript">
              document.getElementById("gene-name").innerHTML = "<?=$name?>";
              </script>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="#"><?=$description?></a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle filter-dropdown" data-toggle="dropdown">
                    Filter Interaction <b class="caret"></b>
                  </a>
                  <ul class="dropdown-menu" id="function-filter">
                    <li><a href="#" data-func="all">Show All</a></li>
                    <li><a href="#" data-func="gain">Gain of Function</a></li>
                    <li><a href="#" data-func="loss">Loss of Function</a></li>
                  </ul>
                </li>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle download-dropdown" data-toggle="dropdown">
                    Download <b class="caret"></b>
                  </a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="../proteins/wt/<?=$_GET['variant']?>.fasta">
                        Download Wildtype Sequence
                      </a>
                    </li>
                    <li><a href="#" id="download_all">Download All Mutant Sequences</a></li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <table class="table table-striped table-hover" id="variant-details-table">
            <thead>
              <tr>
                <th>Mutation ID</th>
                <th>Mutation Type</th>
                <th>Disease/Tissues</th>
                <th>Rewiring Effects - <span class="g">Gain of Function</span>|<span class="r">Loss of Function</span></th>
              </tr>
            </thead>
          <tbody>
          <?php
            $func = isset($_GET['int-filter']) ? $_GET['int-filter'] : 'NA';

            foreach($variants as $var)
            {
              $MID = $_GET['variant'] . '-' . $var[2];

              // If an effect filter is set, check to see if it's in this row's effects to show it.
              if (in_array($func . ' of function' , $effects) || $func == 'NA')
              {
            ?>
            <tr>
              <td><?=$MID?></td>
              <td><?=$var[3]?></td>
              <td><?=$var[1]?></td>
              <td>
              <?php
              $link = '';
              foreach($DPID as $gene => $PID)
              {
                if (isset($effects[$PID]))
                {
                  if ($effects[$PID] == "gain of function" && ($func == 'gain' || $func == 'NA'))
                  {
                    $link = "<a href='../network/?limit=50&genename={$PID}' class='g'>{$gene}</a>";
                  }
                  elseif ($effects[$PID] == "loss of function" && ($func == 'loss' || $func == 'NA'))
                  {
                    $link = "<a href='../network/?limit=50&genename={$PID}' class='r'>{$gene}</a>";
                  }

                  if($link != '')
                  {
                    echo $link . '  ';
                    $link = '';
                  }
                }
              }
              ?>
              </td>
            </tr>
            <?php
              }
            }
            ?>
          </tbody>
        </table>

        <?php if(isset($_GET['tissues'])) { ?>
          <p class="text-center">
            <a id="showall" href="#" class="btn btn-default">
              Click here to view all variants for this protein.
            </a>
          </p>
        <?php } ?>
      </div>

    <script>
      $( document ).ready(function() {
        $("#showall").on("click", function(){
          window.location.href = './details.php?variant=<?=$_GET['variant']?>';
        });
        <?php
          $var_list = "";
          foreach($variants as $var)
          {
            $var_list = $var_list . "," . $_GET['variant'] . '-' . $var[2];
          }
          echo "var variant_list = '{$var_list}';";
        ?>
        $("#download_all").on("click", function(){
          window.location.href = './download_variants.php?variant_ids=' + variant_list;
        });
      });
    </script>
    <?php } else { ?>
      Error: Protein not found.
    <?php } ?>
    <?php include_once('../partials/footer.php'); ?>
        </div>
    	</div>
  	</div>
    <script src="/assets/scripts/variant_details.js"></script>
	</body>
</html>