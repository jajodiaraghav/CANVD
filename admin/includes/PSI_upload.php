<div class="panel panel-primary">
  <div class="panel-heading" id="table-name-header">Please upload a PSI MI-TAB file</div>
  <div class="panel-body">
    <div id="panel-content">
      <div id='fields'>
        <strong>The following fields (Tab seperated columns) are required for this table:</strong>
        <p style='font-size:0.7em'>
          Unique identifier for interactor A, Unique identifier for interactor B, Alternative identifier for interactor A, Alternative identifier for interactor B, Aliases for A, Aliases for B, Interaction detection methods, First author, Identifier of the publication, NCBI Taxonomy identifier for interactor A, NCBI Taxonomy identifier for interactor B, Interaction types, Source databases, Interaction identifier(s), Confidence score, Complex expansion, Biological role A, Biological role B, Experimental role A, Experimental role B, Interactor type A, Interactor type B, Xref for interactor A, Xref for interactor B, Xref for the interaction, Annotations for interactor A, Annotations for interactor B, Annotations for the interaction, NCBI Taxonomy identifier for the host organism, Parameters of the interaction, Creation date, Update date, Checksum for interactor A, Checksum for interactor B, Checksum for interaction, negative,  Feature(s) for interactor A:  Feature(s) for interactor B, Stoichiometry for interactor A, Stoichiometry for interactor B, Participant identification method for interactor A, Participant identification method for interactor B
        </p>
      </div>
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
<div class="panel panel-info">
  <div class="panel-heading" id="table-name-header"><strong>Other operations</strong></div>
  <div class="panel-body">
    <div id="panel-content">
      <a href="update_table.php">Update the "Tissues" table</a>
    </div>
  </div>
</div>