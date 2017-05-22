<div class="panel panel-default">
  <div class="panel-heading" id="table-name-header">
    Please upload either PNG (image) or TXT or PWM files.
  </div>
  <div class="panel-body">
    <div id="panel-content">
      <form action="upload_psi.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
        <div>
          <label>
            <input type="radio" name="action" value="add" checked>
            Append this data to the table
          </label>
        </div>
        <div>
          <label>
            <input type="radio" name="action" value="replace">
            Replace the ENTIRE table with this data
          </label>
        </div>
        <button class="btn btn-sm btn-primary pull-right" type="submit">Submit</button>
      </form>
    </div>
  </div>
</div>