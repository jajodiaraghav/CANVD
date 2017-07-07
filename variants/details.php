<?php
include_once('../common.php');
include_once('../header.php');
?>
<div class="container">
	<div id="content">
    <div class="jumbotron">
      <div class="container">
        <span id="title_fix"></span>
          <p id="main-top-text">
            Variant(s) of <span style="color:#ea2f10" id="gene-name"><?php echo $_GET['variant']?> </span> protein.
          </p>
      </div>
      <?php
      if(isset($_GET['variant'])) {
        $query = 'SELECT GeneName, Description FROM T_Ensembl WHERE EnsPID=:ens;';
        $stmt = $dbh->prepare($query);
        $param = array(':ens'=> $_GET['variant']);
        $stmt->execute($param);
        while ($row = $stmt->fetch())
        {
          $name = $row[0];
          $description = $row[1];
        }

        if(isset($_GET['tissues']))
        {
          $tissue = $_GET['tissues'];
          $tissues = explode(",",$tissue);
          function sanitize($s) { return htmlspecialchars($s); }
          $t = array_map('sanitize', $tissues);
          $P_List = "'" . implode("','", $t) . "'";

          $query = 'SELECT EnsPID, Tumour_Site, Mutation_ID, Mut_Description FROM T_Ensembl
                    INNER JOIN T_Mutations ON T_Ensembl.EnsGID = T_Mutations.Peptide_EnsGID
                    WHERE EnsPID=:ens AND Tumour_Site IN(' . $P_List . ')';
        }
        else
        {
          $query = 'SELECT EnsPID, Tumour_Site, Mutation_ID, Mut_Description FROM T_Ensembl
                    INNER JOIN T_Mutations ON T_Ensembl.EnsGID = T_Mutations.Peptide_EnsGID
                    WHERE EnsPID=:ens';
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
        $mut_syntaxes_to_ids = array();
        $interactions = array();
        foreach($variants as $var)
        {
          $query = 'SELECT WT, MT, Eval, T_Interactions.IID FROM T_Interactions_MT
                    INNER JOIN T_Interactions
                    ON T_Interactions.IID = T_Interactions_MT.IID
                    WHERE Peptide_EnsPID=:ens';
          $stmt = $dbh->prepare($query);
          $query_params = array(':ens'=> $var[0]);
          $stmt->execute($query_params);
          while ($row = $stmt->fetch())
          {
            $effects[$row[0]][$row[3]] = $row[2];
            $mut_syntaxes_to_ids[$row[3]] = $row[0];
          }
        }

        // Get all sh3 interacting domains
        $query = 'SELECT Domain_EnsPID, Peptide_EnsPID, IID FROM T_Interactions WHERE Peptide_EnsPID=:ens';
        $stmt = $dbh->prepare($query);
        $query_params = array(':ens'=> $_GET['variant']);
        $stmt->execute($query_params);
        $domains = array();
        $domain_ids = array();
        while ($row = $stmt->fetch())
        {
          $query2 = 'SELECT GeneName FROM T_Ensembl WHERE EnsPID=:ens';
          $stmt2 = $dbh->prepare($query2);
          $query_params2 = array(':ens'=> $row[0]);
          $stmt2->execute($query_params2);
          //Get Mut syntax of this ID
          if (isset($mut_syntaxes_to_ids[$row[2]])){
            $mut_syntax_d = $mut_syntaxes_to_ids[$row[2]];
            $domain_name = $stmt2->fetch()[0];
            if (isset($domains[$mut_syntax_d])){
              if (!in_array($domain_name, $domains[$mut_syntax_d])){
              $domains[$mut_syntax_d][$row[2]] = $domain_name;
              $domain_ids[$domain_name] = $row[0];
              }
            }
            else {
              $domains[$mut_syntax_d][$row[2]] = $domain_name;
              $domain_ids[$domain_name] = $row[0];
            }
          }
        }
      ?>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="navbar navbar-inverse">
            <div class="navbar-header">
              <a class="navbar-brand" href="#"><?php echo $name;?> (<?php echo $_GET['variant'] ?>)</a>
              <script type="text/javascript">
              document.getElementById("gene-name").innerHTML = "<?php echo $name;?>";
              </script>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="#"><?php echo $description;?></a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle filter-dropdown" data-toggle="dropdown">
                    Filter Interaction <b class="caret"></b>
                  </a>
                  <ul class="dropdown-menu" id="function-filter">
                    <li><a href="#" data-func="all">Show All</a></li>
                    <li><a href="#" data-func="gain">Gain of Function</a></li>
                    <li><a href="#" data-func="loss">Loss of Function</a></li>
                    <li><a href="#" data-func="neutral">Neutral Effect on Function</a></li>
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
                      <a href="../proteins/wt/<?php echo $_GET['variant'];?>.fasta">
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
                <th>Interaction ID</th>
                <th>Mutation Type</th>
                <th>Disease/Tissues</th>
                <th>Rewiring Effects - <span class="g">Gain of Function</span>, <span class="r">Loss of Function</span>.<br></th>
                <th>Download Sequence</th>
              </tr>
            </thead>
          <tbody>
          <?php
            $filter_option = "N/A";
            if (isset($_GET['int-filter']))
            {
              if ($_GET['int-filter'] == "gain")
                $filter_option = "gain of function";
              elseif ($_GET['int-filter'] == "loss")
                $filter_option = "loss of function";
            }

            foreach($variants as $var)
            {
              $mut_id = $_GET['variant'] . '-' . $var[2];

              //If an effect filter is set, check to see if it's in this row's effects to show it.
              if ((isset($_GET['int-filter']) && (isset($effects[$var[1]]) && in_array($filter_option,$effects[$var[1]]))) || (isset($_GET['int-filter']) && $_GET['int-filter'] == 'neutral' && !isset($effects[$var[1]])) || !isset($_GET['int-filter'])) {
            ?>
            <tr>
              <td><?php echo $mut_id;?></td>
              <td><?php echo $var[2];?></td>
              <td><?php echo $var[3];?></td>
              <td><?php echo $var[1];?></td>
              <td>
              <?php
              if (isset($effects[$var[1]])) {
                $i = 0;
                foreach($domains[$var[1]] as $k => $d) {
                  if ($effects[$var[1]][$k] == "gain of function" && ($filter_option == 'gain of function' || $filter_option == "N/A")) {
                      if ($i > 0) {
                        echo ", <a href='../network/?limit=50&genename=" .  $domain_ids[$d] . "' class='g'>" . $d . "</a>";
                      } else {
                        echo "<a href='../network/?limit=50&genename=" .  $domain_ids[$d] . "' class='g'>" . $d . "</a>";
                      }
                    } elseif ($effects[$var[1]][$k] == "loss of function" && ($filter_option == 'loss of function' || $filter_option == "N/A")) {
                      if ($i > 0) {
                        echo ", <a href='../network/?limit=50&genename=" .  $domain_ids[$d] . "' class='r'>" . $d . "</a>";
                      } else {
                        echo "<a href='../network/?limit=50&genename=" .  $domain_ids[$d] . "' class='r'>" . $d . "</a>";
                      }
                    }
                    $i += 1;
                  }
                } else {
                  echo "None";
                }
              ?>
              </td>
              <td><a href="../proteins/mt/<?php echo $mut_id?>.fasta">Download</a></td>
            </tr>
            <?php
              }
            }
            ?>
          </tbody>
        </table>

        <?php if(isset($_GET['tissues'])) { ?>
          <p style="margin-top:20px;margin-bottom:60px;font-size:1.2em;text-align:center;">
            <a id="showall" href="#" class="btn btn-default">
              Click here to view all variants for this protein.
            </a>
          </p>
        <?php } ?>
      </div>

    <script>
      $( document ).ready(function() {
        $("#showall").on("click", function(){
          window.location.href = './details.php?variant=<?php echo $_GET['variant'];?>';
        });
        <?php
          $var_list = "";
          $i = 0;
          foreach($variants as $var) {
            if ($i > 0){
              $var_list = $var_list . "," . $_GET['variant'] . '-' . $var[2];
            } else {
              $var_list = $_GET['variant'] . '-' . $var[2];
            }
            $i += 1;
          }
          echo "var variant_list = '" . $var_list . "';";
        ?>
        $("#download_all").on("click", function(){
          window.location.href = './download_variants.php?variant_ids=' + variant_list;
        });
      });
    </script>
    <?php } else { ?>
      Error: Protein not found.
    <?php } ?>
    <?php include_once('../footer.php'); ?>
        </div>
    	</div>
  	</div>
	</body>
</html>