<?php
include_once('../common.php');
include_once('../header.php');
?>
<div class="container">
	<div id="content">
    <div class="jumbotron">
      <div class="container">
        <span id="title_fix"></span>
          <p><?php include('../logo.php') ?> Variants Search</h2></p><br/>
          <p id="main-top-text" class="text-justify">The <?php include('../logo.php') ?> variants search feature provides the information and full sequences of the disease variants and the corresponding wildtype proteins. This feature helps in building custom variants database in FASAT format that can be used in identifying disease variants using MS/MS-based proteomics, for instance.</p>
          <div class="row">
          <?php if(isset($_GET['search'])) { ?>
            <div class="col-md-12">
              <div class="navbar navbar-inverse">
                <div class="navbar-header">
                  <a class="navbar-brand" href="#">Variants Browser</a>
                </div>
                <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav">
                  <li class="active">
                    <a href="#">Showing <span id="current_count"></span> 
                    out of <span id="total_num"></span> Variants in <span id="prot_current"></span> 
                    out of <span id="prot_num"></span> Proteins</a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle filter-dropdown" data-toggle="dropdown">
                      Filter Disease/Tissue <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" id="filter-tissue-list">
                        <li><a href='#' class='tissue-filter' data-tissue='ALL'>All Diseases/Tissues</a></li>
                    </ul>
                  </li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle download-dropdown" data-toggle="dropdown">
                      Download <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="#" id='download-current'>Download These Variants</a></li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>

            <table class="table table-striped table-hover" id="variant-table">
              <thead>
                <tr>
                  <th>Disease/Tissue</th>
                  <th>Protein ID</th>
                  <th>Protein Name</th>
                  <th>Variants in tissue(s)</th>
                  <th>Interactions (Total for this protein)</th>
                  <th>Rewiring Effects (Total for this protein)</th>
                </tr>
              </thead>
              <tbody id="variants-results">
                <?php include('./variant_load.php');?>
                <script>
                var tissues_selected = <?php echo json_encode($_GET['tissue']);?>;
                var tissues_original = <?php echo json_encode($_GET['tissue']);?>;
                var mut_types = <?php echo json_encode($_GET['mut_type']);?>;
                var prot_name = <?php echo json_encode($_GET['prot']);?>;
                var prot_source = <?php echo json_encode($_GET['source']);?>;
                var processing;
                </script>
                <script type="text/javascript" src ="/assets/scripts/variants.js"></script>
              </tbody>
            </table>
          </div>
        <?php } else { ?>

            <form class="form-horizontal" id="target" method="get">
              <input type='hidden' value='true' name="variant_search">
                <input type="hidden" name="search" value="yes">
                  <fieldset>
                    <legend style='font-weight:300;'>Search for Protein Variants</legend>
                      <div class="col-md-5">
                        <div class="form-group">
                          <label class="col-md-3 control-label" for="tissue-input">Protein Names / IDs (separate by comma)</label>
                          <div class="col-md-8">
                            <input id="tissue-input" name="prot" type="text" placeholder="search for specific variant proteins" class="form-control input-md">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label" for="variant-effect">Mutation Type</label>
                          <div class="col-md-7">
                          <?php
                            $query = "SELECT DISTINCT mut_description FROM T_Mutations;";
                            $query_params = array();
                            $stmt = $dbh->prepare($query);
                            $stmt->execute($query_params);
                            while ($row = $stmt->fetch()) {
                          ?>
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" checked name="mut_type[]" id="variant-effect-0" value="<?php echo $row[0] ?>">
                              <?php echo $row[0] ?>
                            </label>
                          </div>
                          <?php } ?>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-md-3 control-label">Variant Data Sources</label>
                        <div class="col-md-7">
                        <?php
                          $query = "SELECT DISTINCT Source FROM T_Mutations;";
                          $query_params = array();
                          $stmt = $dbh->prepare($query);
                          $stmt->execute($query_params);
                          while ($row = $stmt->fetch()) {
                        ?>
                        <label class="checkbox-inline" for="data-source-box-0">
                          <input type="checkbox" checked name="source[]" id="data-source-box-0" value="<?php echo trim($row[0]); ?>">
                          <?php echo $row[0]; ?>
                        </label>
                        <?php } ?>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-5">
                    <div class="form-group">
                      <label class="col-md-4 control-label" for="selectmultiple">
                        Select Specific Diseases/Tissues
                      </label>
                      <div class="col-md-7">
                        <select id="selectmultiple" name="tissue[]" class="form-control" multiple="multiple" style="height:160px;">
                        <?php
                          $query = "SELECT * FROM tissue_table_browser;";
                          $query_params = array();
                          $stmt = $dbh->prepare($query);
                          $stmt->execute($query_params);
                          $tissues = array();
                          while ($row = $stmt->fetch()) {
                        ?>
                        <option value="<?php echo $row[1];?>">
                          <?php echo ucwords(str_replace("_"," ", $row[1]));;?>
                        </option>
                        <?php } ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <button id="singlebutton" name="singlebutton" class="btn btn-danger" style="margin-top:135px;">Search</button>
                    </div>
                  </div>
                </fieldset>
              </form>
            <?php } ?>
            <?php include '../footer.php'; ?>
          </div>
        </div>
    	</div>

<div id="download_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Downloading variants <i class="fa fa-cloud-download"></i></h4>
      </div>
      <div class="modal-body" style="padding:20px;font-style:italic;">
        <p>
          <i class="fa fa-database"></i>
          Preparing custom variant dataset, please wait... (this could take up to 5 minutes)
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <i> Dismiss this message </i>
        </button>
      </div>
    </div>
  </div>
</div>

	</body>
</html>