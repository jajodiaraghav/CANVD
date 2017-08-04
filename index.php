<?php
include_once('common.php');
include_once('partials/stats.php');
include_once('partials/header.php');
?>
  <div class="container">
	  <div id="content">
      <div class="jumbotron">
        <div class="container">
          <span id="title_fix"></span>
          <p class="about"><span><strong>DV<span style="color: #ea2f10;">-IMPACT</span></strong></span> Database is an online resource for disease variants and their impacts on domain-peptide protein interaction networks. <a href='faqs/'>Read more...</a></p>
      	</div>
      	<div class="row">
      		<div class="col-md-3">
      			<div class="list-group">
      			  <li class="list-group-item">Can-VD Statistics</li>
      			  <li class="list-group-item">Proteins
                <span data-color="alert-info" class="badge">
                <?php echo number_format(intval($protein_count));?>
                </span>
              </li>
              <li class="list-group-item">Domains/PWMs
                <span data-color="alert-info" class="badge">
                <?php echo number_format(intval($domain_count)) .' / '. number_format(intval($pwm_count));?>
                </span>
              </li>
      		    <li class="list-group-item">Interactions
                <span data-color="alert-info" class="badge">
                <?php echo number_format(intval($interaction_count));?>
                </span>
              </li>
              <li class="list-group-item">Variants
                <span data-color="alert-info" class="badge">
                <?php echo number_format(intval($mutation_count));?>
                </span>
              </li>
              <li class="list-group-item">Rewiring Effects
                <span data-color="alert-info" class="badge">
                <?php echo number_format(intval($rewire_count));?>
                </span>
              </li>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <a href="./announce/">Announcements and News</a>
              </div>
              <div class="panel-body">
              <?php
                $query = 'SELECT * FROM announcements WHERE `show_homepage`=1 AND `show`=1 ORDER BY id DESC;';
                $stmt = $dbh->prepare($query);
                $stmt->execute();
                while ($row = $stmt->fetch()) {
              ?>
                <div style="margin-bottom:15px;">
                  <span>
                    <a href="./announce/"><?php echo $row[2];?></a><br>
                    <small><?php echo $row[1];?></small>
                  </span>
                  <?php echo substr($row[3], 0, strpos($row[3], ".") + 1); ?>
                  <br><a href='announce/'>Read more...</a>
                </div>
              <?php } ?>
              </div>
            </div>
		      </div>

		      <div class="col-md-9">
		        <form id="search_form" action="./network/" method="get" >
              <div class="input-group input-group-lg">
                <input type="search" id="search_input" name="genename" class="form-control" placeholder="Enter a protein name or Ensembl ID. Examples: LYN, CRK, ENSP00000348602" >
                  <span class="input-group-btn">
  		              <button type="submit" class="btn btn-danger" type="button" id="search_btn">Search</button>
                    <button class="btn btn-default" type="button" id="advanced_btn">Advanced</button>
		              </span>
                </div>
              <div id="advanced-search-box" style="display:none">
                <div class="form-signin">
                  <div class="col-md-12" style="margin-bottom:10px;">
                    <h3 style="">Mutation Types</h3>
                	</div>
	                <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Mutation Type</label>
                        <input type='hidden' value='true' name="main_search">
                        <?php
                        //Get all Mutation Types
                        $query = "SELECT DISTINCT Mut_Description FROM T_Mutations;";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute();
                        while ($row = $stmt->fetch()) {
                        ?>
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" checked name="mut_type[]" id="variant-effect-0"
                            value="<?php echo $row[0] ?>">
                          <?php echo $row[0] ?>
                          </label>
                        </div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Variant Data Sources</label><br>
                        <?php
                        //Get all database sources.
                        $query = "SELECT DISTINCT Source FROM T_Mutations;";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute();
                        while ($row = $stmt->fetch()) {
                        ?>
                        <label class="checkbox-inline" for="data-source-box-0">
                          <input type="checkbox" checked name="source[]" id="data-source-box-0"
                          value="<?php echo trim($row[0]); ?>">
                          <?php echo $row[0]; ?>
                        </label>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            <div id="browse-and-tabs">
        			<ul class="nav nav-tabs" id="browse-tabs">
        			  <li class="active"><a data-tab="protein">Tissues/Cancer Types</a></li>
        			  <li><a data-tab="cancer">Proteins</a></li>
        			  <li><a data-tab="tumor">PWMs</a></li>
        			</ul>
              <div id="tissue-table">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>Tissue</th>
                      <th>Total Variants (Mutations)</th>
                      <th>Gain of Interaction</th>
                      <th>Loss of Interaction</th>
                      <th>Total Proteins</th>
                      <th>Details</th>
                    </tr>
                  </thead>
                  <tbody id="tissue-table-body"></tbody>
                </table>
                <ul class="pager" id="tissue-page" data-page=0>
                  <li><a id="tissue-back">Previous</a></li>
                  <span class='num-viewer'> Viewing 
                  <span id="tissue-start">1</span>-
                  <span id="tissue-end">10</span> of <span id="tissue-total">40</span></span>
                  <li><a id="tissue-forward">Next</a></li>
                </ul>
              </div>

              <script src="assets/scripts/home.js"></script>

              <div id="protein-table">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>Protein ID</th>
                      <th>Protein Name</th>
                      <th>Domain</th>
                      <th>Gain of Interactions</th>
                      <th>Loss of Interactions</th>
                      <th>Total Interactions</th>
                      <th>Details</th>
                    </tr>
                  </thead>
                  <tbody id="protein-table-body">
                  </tbody>
                </table>
                <ul class="pager" id="protein-page" data-page=0>
                  <li><a id="protein-back">Previous</a></li>
                  <span class='num-viewer'> Viewing 
                  <span id="protein-start">1</span>-
                  <span id="protein-end">20</span> of <span id="protein-total">40</span></span>
                  <li><a id="protein-forward">Next</a></li>
                </ul>
              </div>

              <div class="modal fade" id="testing">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title">PWM Logo</h4>
                    </div>
                    <p id="actual-image-content" class="row text-center"></p>
                  </div>
                </div>
              </div>

              <div id="pwm-table">
                <table class="table table-striped table-hover" id="pwm-actual-table">
                  <thead>
                    <tr>
                      <th>PWM</th>
                      <th>Logo</th>
                      <th>PWM</th>
                      <th>Logo</th>
                    </tr>
                  </thead>
                  <tbody id="pwm-table-body"></tbody>
                </table>
                <ul class="pager" id="pwm-page" data-page=0>
                  <li><a id="pwm-back">Previous</a></li>
                  <span  class='num-viewer'> Viewing 
                    <span id="pwm-start">1</span>-
                    <span id="pwm-end">20</span> of <span id="pwm-total">40</span>
                  </span>
                  <li><a id="pwm-forward">Next</a></li>
                </ul>
              </div>
            </div>
  		    </div>
        </div>
		    <?php include_once('partials/footer.php'); ?>
      </div>
	  </div>
	</body>
</html>