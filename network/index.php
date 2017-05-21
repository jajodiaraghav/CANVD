<?php
  include_once('../common.php');
  include_once('../header.php'); 
?>
<div class="container">
	<div id="content">
    <div class="jumbotron">
      <div class="container">
        <span id="title_fix"></span>   
        <p>
        <h2 style="margin-bottom:30px;"<?php include('../logo.php') ?> PPI Search</h2></p><br/>
        <p id="main-top-text">
          The impacts of missense mutations on <b id="prot-name" style="color:#ea2f10"></b> protein interactions in <b id="tumor_c2" style="color:#ea2f10"></b> disease(s)/tissue(s).
        </p>
      </div>
      <div class="test" id="network-selection-container"></div>
      <div class="container" id="network-view-container">
        <div class="row">
          <div class="col-md-2">
            <ul class="nav nav-pills">
              <li id="filters_li" class="active"><a href="#" id="filters_btn">Filters</a></li>
              <li id="downloads_li"><a href="#" id="downloads_btn">Download</a></li>
            </ul>
              <div id="filters_panel">
                <div class="list-group show-list" id="domains_list">
                  <span class="list-group-item"><b>Domains</b></span>
                </div>
                <div class="list-group show-list" id="sources_list">
                  <span class="list-group-item"><b>Mutation Sources:</b></span>
                </div>
                <div class="list-group show-list" id="tissue_list">
                  <span class="list-group-item"><b>Interactions:</b></span>
                </div>
              </div>
              <div id="downloads_panel">
              <p style="font-size:1.2em;">
                There are multiple datasets which can be downloaded for this domain's network.
              </p>

              <div class="list-group">
                <span class="list-group-item download-list" style="background:#d6d6d6">Download - Current view (csv):</span>
                <a href="#" class="list-group-item vd interaction-download">Download Interactions</a>
                <a href="#" class="list-group-item vd effects-download">Download Mutation Effects</a>
                <a href="#" class="list-group-item vd mutation-download">Download Mutation List</a>
              </div>

              <div class="list-group">
                <span class="list-group-item download-list" style="background:#d6d6d6">Download - Entire Network (csv):</span>
                <a href="#" class="list-group-item ad interaction-download">Download Interactions</a>
                <a href="#" class="list-group-item ad effects-download">Download Mutation Effects</a>
                <a href="#" class="list-group-item ad mutation-download">Download Mutation List</a>
              </div>
              </div>
            </div>

            <div class="col-md-8" id="main_network_column" style="padding-right:0;">
              <div id="cy"></div>
            </div>

            <div class="col-md-2" style="padding:0;margin:0;">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title stats-title">Network Statistics</h3>
                </div>
                <div class="panel-body stats-body">
                  <p>
                  <b id="prot_c"></b> proteins interact with the <b id="dm_c"></b> domain of <b id="prot-name2"></b> In <b id="tumor_c"></b> disease(s)/tissues(s).</p>
                  <p style="font-size:0.7em !important;">Click on protein nodes to view mutations and interaction effects.</p>
                </div>
              </div>

              <div class="list-group show-list">
                <span class="list-group-item"><b>Mutation Effect</b></span>
                <a class="list-group-item show-item no-prot no-int">
                  <span data-color="noch-pt" class="badge noch-pt" ></span>Neutral
                </a>
                <a class="list-group-item show-item g-prot gain-int">
                  <span data-color="gn-pt" class="badge gn-pt" ></span>Gain of Interaction
                </a>
                <a class="list-group-item show-item l-prot loss-int">
                  <span data-color="ls-pt" class="badge ls-pt" ></span>Loss of Interaction
                </a>
                <a class="list-group-item show-item bo-prot bo-int">
                  <span data-color="bo-pt" class="badge bo-pt" ></span>Both Gain and Loss
                </a>
              </div>

              <div class="list-group show-list">
                <span class="list-group-item"><b>Layout Options</b></span>
                <a class="list-group-item layout-select layout-circle show-item active">Circle</a>
                <a class="list-group-item layout-select layout-grid show-item">Grid</a>
              </div>
            </div>
          </div>
        <?php include '../footer.php'; ?>
      </div>
    </div>

    <script type="text/javascript">
      <?php if (isset($_GET['limit'])) { ?>
        var net_limit = <?php echo $_GET['limit']; ?>;
      <?php } else { ?>
        var net_limit = 50;
      <?php } ?>

      <?php
        include_once('../search.php'); //Generate network data
      ?>
      console.log(networkData);
    </script>
    <script scr="/assets/scripts/network.js" type="text/javascript"></script>
  </body>
</html>