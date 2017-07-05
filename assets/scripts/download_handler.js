$(function() {

    // Handles downloads
    $("#downloads_panel").on("click","a", function() {

        var download_proteins = [];
        if ($(this).hasClass("vd"))
        {
            // Get list of visible IDs
            $.each(cy.nodes(":visible").jsons(), function(index, value) {
                download_proteins.push(value.data.id);
            });
        }
        else
        {            
            download_proteins = Object.keys(networkData[active_domain].geneInfo); // Get list of all ids
        }

        // Get information depending on type.
        var netdata = networkData[active_domain], data = "";
        if ($(this).hasClass("interaction-download"))
        {
            data = "Domain,InteractionPartner,Score,Start,End\n";
            for (inter in netdata.raw_interactions)
            {
                var int = netdata.raw_interactions[inter];
                if ($.inArray(int.interaction, download_proteins) != -1)
                    data = data + int.domain + "," + int.interaction + "," + int.score + "\n";
            }
        }
        else if ($(this).hasClass("effects-download"))
        {
            data = "Domain,InteractionPartner,WT,MT,WTscore,MTscore,Mut_Syntax,DeltaScore,Log2Score,Eval\n";
            for (inter in netdata.mut_effects)
            {
                if ($.inArray(inter, download_proteins) != -1)
                {
                    var effects = netdata.mut_effects[inter];
                    for (eff in effects)
                    {
                        effect = effects[eff];
                        data = data + netdata.domain_info.EnsPID +","+inter+","+effect[0]+","+effect[1]+","+effect[2]+","+effect[3]+","+effect[4]+","+effect[7]+","+effect[5]+","+effect[6]+ "\n";
                    }
                }
            }
        }
        else if ($(this).hasClass("mutation-download"))
        {
            data = "EnsPID,Syntax,Tissue,Source\n";
            for (mut in netdata.muts)
            {
                var mutation = netdata.muts[mut];
                if ($.inArray(mutation.EnsPID, download_proteins) != -1)
                    data = data + mutation.EnsPID + "," + mutation.Syntax + "," + mutation.Tissue + "," + mutation.Source + "\n";
            }
        }

        $.ajax({
            url:"./save_data.php",
            type: "POST",
            data: {download_data:data},
            success: function(result) {
                window.location.href = './download.php';
            }
        });
    });
});
