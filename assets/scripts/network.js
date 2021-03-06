$(function() {
	function update() {
		var i = 0, limit = start + 100, gene_info = {}, mut_ints = {}, muts = {};
		for (gene in networkData[active_domain].gene_info)
		{
			if (i >= start && i < limit)
				gene_info[gene] = networkData[active_domain].gene_info[gene];
			i += 1;
		}

		//get mut_int with just those protein ids (mut_int)
		for (mut_int in networkData[active_domain].mut_int)
		{
			if ($.inArray(mut_int, Object.keys(gene_info)) != -1)
				mut_ints[mut_int] = networkData[active_domain].mut_int[mut_int];
		}

		//get mut with just those protein ids (muts)      
		for (mut in networkData[active_domain].muts)
		{
			var mut_prot = networkData[active_domain].muts[mut].EnsPID;
			if ($.inArray(mut_prot, Object.keys(gene_info)) != -1)
				muts[mut] = networkData[active_domain].muts[mut];
		}

		$("#filters_panel").find(".viewing_more").remove();
		if (parseInt(Object.keys(networkData[active_domain].gene_info).length) > limit)
			$("#filters_panel").prepend('<div class="viewing_more"><p style="font-size:0.9em;margin-bottom:6px;padding-bottom:0;">Viewing ' + start.toString() + '-'+ limit.toString() + ' out of ' + Object.keys(networkData[active_domain].gene_info).length + ' total interactions. </p><div class="btn btn-default btn-sm" style="margin-bottom:10px;" id="show_more"> Show more interactions</div></div>');
		else if (parseInt(Object.keys(networkData[active_domain].gene_info).length) > 100)
			$("#filters_panel").prepend('<div class="viewing_more"><p style="font-size:0.9em;margin-bottom:6px;padding-bottom:0;">Viewing ' + start.toString() + '-'+ Object.keys(networkData[active_domain].gene_info).length + ' out of ' + Object.keys(networkData[active_domain].gene_info).length + ' total interactions. </p></div>');

		// Reset tissue filters
		$('.tissue-item').find(".badge").addClass("tissue_active");
		var domain_type = networkData[active_domain].domain_info.Type;
		var prot_num = Object.keys(gene_info).length;
		var gene_name = networkData[active_domain].domain_info.DomainName;
		$("#prot_c").text(prot_num);
		$("#dm_c").text(domain_type);
		$("#prot-name2").text(gene_name);
		$("#prot-name").text(gene_name);
		//count mutation effects
		var gain_count = 0;
		var loss_count = 0;
		var neutral_count = 0;
		var both_count = 0;
		for (int in mut_ints)
		{
			interaction = mut_ints[int];
			if (interaction == "gain") gain_count = gain_count + 1;
			else if (interaction == "loss") loss_count = loss_count + 1;
			else if (interaction == "neutral") neutral_count = neutral_count + 1;
			else if (interaction == "both") both_count = both_count + 1;
		}
		$(".noch-pt").text(neutral_count);
		$(".gn-pt").text(gain_count);
		$(".ls-pt").text(loss_count);
		$(".bo-pt").text(both_count);
		// Get tissue unique list
		tissues = [];
		mutations = muts;
		sources = {};
		for (mut in mutations)
		{
			if ($.inArray(mutations[mut].Tissue, tissues) == -1)
				tissues.push(mutations[mut].Tissue);
			if ($.inArray(mutations[mut].Source, Object.keys(sources)) != -1)
				sources[mutations[mut].Source] =  sources[mutations[mut].Source] + 1;
			else
				sources[mutations[mut].Source] = 1;
		}
		$("#tumor_c").text(tissues.length);
		$("#tumor_c2").text(tissues.length);

		for (source in Object.keys(sources))
		{
			sour = Object.keys(sources)[source]
			$("#sources_list").find(".show-item").remove();
			$("#sources_list").append('<span class="list-group-item show-item source-item"><span class="badge">'  + sources[sour] + '</span>'+ sour + '</span>');
		}

		//Count proteins per tissue; sort tissues
		tissue_to_protein = {};
		tissue_proteins = {};
		for (tissue in tissues)
		{
			//add all proteins with mutations in this tissue to {tissue_name: [array]}
			tissue_to_protein[tissues[tissue]] = [];
			tissue_proteins[tissues[tissue]] = 0;
			for (mut in mutations)
			{
				if (tissues[tissue] == mutations[mut].Tissue)
				{
					if ($.inArray(mutations[mut].EnsPID, tissue_to_protein[tissues[tissue]]) == -1)
					{
						tissue_to_protein[tissues[tissue]].push(mutations[mut].EnsPID);
						tissue_proteins[tissues[tissue]] += 1;
					}
				}
			}
		}

		//sort tissues by size
		tissues_sorted = Object.keys(tissue_proteins).sort(function(a,b){ return tissue_proteins[a] - tissue_proteins[b]}).reverse();
		
		//Create tissue list
		$("#tissue_list").find(".show-item").remove();
		var i = 0;
		for (tissue in tissues_sorted)
		{
			if (i < 4)
				$("#tissue_list").append('<a class="list-group-item tissue-a show-item "><span class="badge tissue-item tissue_active">'  + tissue_to_protein[tissues_sorted[tissue]].length.toString() + '</span><span class="tissue_name">'+ tissues_sorted[tissue] + '</span></a>');
			else
			{
				$("#tissue_list").append('<a class="list-group-item show-item tissue_load_more" style="background:#E7E7E7">Load More</a>');
				break;
			}
			i += 1;
		}

		//Add nodes and edges
		net_nodes = [];
		net_edges = [];
		net_nodes.push({ data: { id: gene_name, color: "#d12e2e", name: gene_name, weight: 100, center: "true"}} )
		var interactions = mut_ints;
		var interaction_data = gene_info;
		for (node in interaction_data)
		{
			//Add nodes
			net_nodes.push( { data: { id: node, name: interaction_data[node].GeneName, description: interaction_data[node].Description, color: "#292929", gene_id: node, weight: 100, muts: "", mut_type: interactions[node]}} );

			//Add edges
			var edge_color, edge_style;
			if (interactions[node] == "loss")
				edge_color = "#d7191c", edge_style = "dashed";
			if (interactions[node] == "gain")
				edge_color = "#2c7bb6", edge_style = "dashed";
			if (interactions[node] == "both")
				edge_color = "#c13e86", edge_style = "solid";
			if (interactions[node] == "neutral")
				edge_color = "#fdb863", edge_style = "solid";

			//CHANGE TO AVERAGE AVG EDGE WIDTH EDGE WEIGHT
			var edge_width = parseFloat(networkData[active_domain].interaction_eval[node]['Avg']*7)
			if (edge_width < 1) edge_width = 1;
			net_edges.push( { data: { source: gene_name, target: node, content: "Interaction", width:3, type: interactions[node], width:(edge_width), color: edge_color, style: edge_style } });
		}

		options = {
			 layout: { name: layoutType },
			 name: 'circle',
			 showOverlay: false,
			 fit:true,
			 hideEdgesOnViewport: true,
			 hideLabelsOnViewport: true,
			 boxSelectionEnabled: false,
			 style: cytoscape.stylesheet()
				.selector('node')
				  .css({
					 'background-color': 'data(color)',
					 'content': 'data(name)',
					 'font-size': 14,
					 'text-valign': 'center',
					 'color': 'white',
					 'text-outline-color': 'data(color)',
					 'text-outline-width':'3',
					 'height': '40',
					 'width':'40',
					 'border-color': '#fff',
					 'shadow-blur': 0
				  })
				.selector(':selected')
				  .css({
					 'background-opacity':'0.9',
					 'text-outline-opacity':'0',
					 'line-color': '#000',
					 'target-arrow-color': '#000',
					 'border-color':'#398bc6',
					 'border-opacity':'0.8',
					 'border-width':'2',
					 'shadow-blur': 0
				  })
				.selector('edge')
				  .css({
					 'width': 3,
					 'line-color': 'data(color)',
					 'line-style': 'data(style)',
					 'opacity':'0.9',
					 'width':'data(width)',
					 'target-arrow-shape': 'triangle',
					 'target-arrow-color': 'data(color)',
				  }),
			elements: { nodes: net_nodes, edges: net_edges },
			ready: function() {
				cy = this;
				cy.userZoomingEnabled(0);
				cy.boxSelectionEnabled(0);
				cy.userPanningEnabled(0);
				var clickedNode = null;
				var clickedEdge = null;
				cy.$('node').on('click', function(e){
					var ele = e.cyTarget;
					clickedNode = ele;
				});

				//Popups
				cy.elements('node').qtip({
				content: {
				  text: function(event, api) {
						// Find the protein's interaction edge data
						if (clickedNode.data("center") != "true")
						{
							//Show information
							var html = "<div style='overflow-y:scroll;max-height:320px;'><h6 style='margin-top:0;margin-bottom:8px;'><a href='http://ensembl.org/id/"+clickedNode.data("gene_id")+"' target=\"_blank\">" + clickedNode.data("gene_id") + "</a><p style='margin-top:6px;margin-bottom:0;font-style:italic;'>" + clickedNode.data("description") + "</p></h6>";

							//Show mutation effects
							for (enspid in networkData[active_domain].mut_effects)
							{
								if (enspid == clickedNode.data("gene_id"))
								{
									for (mut_effect in networkData[active_domain].mut_effects[enspid])
									{
										var effects = networkData[active_domain].mut_effects[enspid][mut_effect];
										if (effects[5] == "gain of function")
											var color = "#2C7BB6";
										else
											var color = "#D7191C";

										var wt_seq = "";
										var mt_seq = "";
										for ( var i = 0; i < effects[1].length; i++ )
										{
											if (effects[0].charAt(i) != effects[1].charAt(i))
											{
												wt_seq = wt_seq + "<span class='wt'>" + effects[0].charAt(i) + "</span>";
												mt_seq = mt_seq + "<span class='mt'>" + effects[1].charAt(i) + "</span>";
											}
											else
											{
												wt_seq += effects[0].charAt(i);
												mt_seq += effects[1].charAt(i);
											}
										}

										html = html
												+ '<h4 style="padding-left:10px">Mutation Effect</h4>'
												+ '<h6>Effect: <strong style="color:'+color+'">'+effects[5]+'</strong></h6>'
												+ '<h6>WT Sequence: '+wt_seq+'<br>MT Sequence: '+mt_seq+'</h6>'
												+ '<img src="../pwms/logos/'+networkData[active_domain].pwm+'.png" height="100px" class="pwm-img">'
												+ '<h6>WT Score: '+effects[2]+', MT Score: '+effects[3]+'</h6>'
												+ '<h6>DeltaScore: '+effects[6]+', LOG2 Score: '+effects[4]+'</h6>';
									}
									break;
								}
							}

							// Show mutation list
							var muts_string ="<h6 style='font-weight:bold'>Mutations in this protein:</h6><table class='table table-striped muts-table'><thead><tr><th>Source</th><th>Tumor</th></tr></thead><tbody>";
							extra_rows = "";
							for (mut in networkData[active_domain].muts)
							{
								if (networkData[active_domain].muts[mut].EnsPID == clickedNode.data("gene_id"))
								{
									var this_mut = networkData[active_domain].muts[mut];
									if (this_mut.Impact == -1)
									{
										var row_string = "<tr class='highlighted_mut_l'><td>" + this_mut.Source + "</td><td>" + this_mut.Tissue + "</td></tr>";
										muts_string += row_string;
									}
									else if (this_mut.Impact == 1)
									{
										var row_string = "<tr class='highlighted_mut_g'><td>" + this_mut.Source + "</td><td>" + this_mut.Tissue + "</td></tr>";
										muts_string += row_string;
									}
									else
									{
										var row_string = "<tr><td>" + this_mut.Source + "</td><td>" + this_mut.Tissue + "</td></tr>";
										extra_rows += row_string;
									}
								}
							}

							muts_string += extra_rows;
							return html + muts_string + "</tbody></table></div>";
						}
						else
						{
							return "<div style='padding:10px;font-size:1.5em;line-height:1.8em;'><div style='font-size:14px;line-height:16px;margin-bottom:5px;'>" +
							networkData[active_domain].domain_info.Description.split("[")[0] +
							"</div><p><b>" + networkData[active_domain].domain_info.Type + " domain. Ensembl ID(s):</b><br><a href='http://ensembl.org/id/" + networkData[active_domain].domain_info.EnsPID + "'>" + networkData[active_domain].domain_info.EnsPID + "</a></p></div>";
						}
					},
					title: function(event, api) { return clickedNode.data("id"); }
				},
				show: { ready: false },
				position: { my: 'top center', at: 'bottom center' },
				style: { classes: 'qtip-light', tip: { width: 10, height: 8 }}
			});

			//Edge popups
			cy.$('edge').on('click', function(e){
				var ele = e.cyTarget;
				clickedEdge = ele;
			});

			cy.elements('edge').qtip({
				content: {
					text: function(event, api) {
						var eval_data = networkData[active_domain].interaction_eval[clickedEdge.data("target")];
						var html = "<div style='overflow-y:scroll;max-height:200px;'><h5 style='margin-left:10px;'>Interaction Evaluation:</h5>"
						var muts_string = "<table class='table table-striped muts-table'><thead><tr><th>Property</th><th>Value</th></tr></thead><tbody>";
						muts_string += "<h6><b>Evaluation Score: " + eval_data['Avg'] + "</b></h6>";
						for (m in eval_data) {
							if (isNaN(m) && m != 'IID'  && m != 'Avg'  )
							{
								var scale = parseFloat(eval_data[m]);
								var col = '';
								if (scale < 0.2) col = '#ffffb2';
								else if (scale < 0.4) col = '#fecc5c';
								else if (scale < 0.6) col = '#fd8d3c';
								else if (scale < 0.8) col = '#f03b20';
								else if (scale <= 1) col = '#bd0026';
								muts_string += "<tr><td>" + m.replace("_"," ").replace("_"," ").replace("_"," ") + "</td><td style='background:" + col + "'></td></tr>";
							}
						}
						muts_string += "<svg width='120' height='24' style='margin-left:20px;'><rect fill='#ffffb2' width='24' height='24' x='0'></rect><rect fill='#fecc5c' width='24' height='24' x='24'></rect><rect fill='#fd8d3c' width='24' height='24' x='48'></rect><rect fill='#f03b20' width='24' height='24' x='72'></rect><rect fill='#bd0026' width='24' height='24' x='96'></rect></svg>";
						return html + muts_string +  "</tbody></table></div>";
					},
				},
				show: { ready: false},
				position: { my: 'top center', at: 'bottom center' },
				style: { classes: 'qtip-light', tip: { width: 10, height: 8 }}
			});

			}
		};
		$('#cy').cytoscape(options);
	}

	var active_domain = "";
	for (active_domain in networkData) break;

	//Create domain list
	for (domain in networkData)
	{
		if (domain == active_domain)
			$("#domains_list").append('<a data-domain="' + domain + '" class="list-group-item show-item active"><span class="badge domain">' + Object.keys(networkData[domain].gene_info).length + '</span>'+ networkData[domain].domain_info.DomainName + ' : ' + networkData[domain].domain_info.Type + ' </a>');
		else
			$("#domains_list").append('<a data-domain="' + domain + '" class="list-group-item show-item"><span class="badge domain">'  + Object.keys(networkData[domain].gene_info).length + '</span>'+ networkData[domain].domain_info.DomainName + ' : ' + networkData[domain].domain_info.Type + ' </a>');
	}

	if (networkData.length != 0)
	{
		if (Object.keys(networkData[active_domain].gene_info).length > 50) {
			layoutType = "grid";
			$(".layout-grid").addClass("active");
			$(".layout-circle").removeClass("active");
		}
		else {
			layoutType = "concentric";
			$(".layout-grid").removeClass("active");
			$(".layout-circle").addClass("active");
		}
		var start = 0;
		update();
	}
	else
		$("#main_network_column").prepend("<div class=\"alert alert-warning\" style='margin-right:50px;margin-left:50px;'><p class='lead' style='color:white;'>Error: That protein name or ID was not found in the database.</p></div>");

	// On clicking domain
	$("#domains_list a").on('click', function(){
		active_domain = $(this).data("domain");
		$("#domains_list a").removeClass("active");
		$(this).addClass("active");

		if (Object.keys(networkData[active_domain].gene_info).length > 50){
			layoutType = "grid";
			$(".layout-circle").removeClass("active");
			$(".layout-grid").addClass("active");
		}
		else {
			layoutType = "concentric";
			$(".layout-grid").removeClass("active");
			$(".layout-circle").addClass("active");
		}
		start = 0;
		update();
	});

	 //Show more variants
	 $("body").on("click", "#show_more", function(){
		  start += 100;
		  update();
	 });
	//On changing layout
	$(".layout-select").on("click", function() {
		$(".layout-select").removeClass("active");
		$(this).addClass("active");
		if ($(this).text() == "Circle") layoutType = "concentric";
		else layoutType = "grid";
		update();
	});

	//Tissue load more
	$("#tissue_list").on("click", ".tissue_load_more", function(){
		$(".tissue_load_more").hide();
		var i = 0;
		for (tissue in tissues_sorted)
		{
			if (i >= 4)
				$("#tissue_list").append('<a class="list-group-item tissue-a show-item"><span class="badge tissue-item tissue_active">'  + tissue_to_protein[tissues_sorted[tissue]].length.toString() + '</span><span class="tissue_name">'+ tissues_sorted[tissue] + '</span> </a>');
			i += 1;
		}
	});

	 //Tissue filtering
	 $("#tissue_list").on("click", ".tissue-a", function() {
		if ( $(this).find(".badge").hasClass("tissue_active"))
		{
			$(this).find(".badge").removeClass("tissue_active");
			var turn_on = 0;
		}
		else
		{
			$(this).find(".badge").addClass("tissue_active");
			var turn_on = 1;
		}

		var tissue_type = $(this).find(".tissue_name").html();
		for (tissue in tissue_to_protein)
		{
			tis = tissue_to_protein[tissue];
			if (tissue == tissue_type)
			{
				for (protein in tis)
				{
					prot = tis[protein];
					if (turn_on)
						cy.$("node[id='"+prot+"']").show();
					else
						cy.$("node[id='"+prot+"']").hide();
				}
			}
		}
	});

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
        else if ($(this).hasClass("ad"))
        {            
            download_proteins = Object.keys(networkData[active_domain].gene_info); // Get list of all ids
        }

        // Get information depending on type.
        var netdata = networkData[active_domain], data = "";
        if ($(this).hasClass("interaction-download"))
        {
            data = "Domain,InteractionPartner\n";
            for (inter in netdata.raw_interactions)
            {
                var int = netdata.raw_interactions[inter];
                if ($.inArray(int.interaction, download_proteins) != -1)
                    data = data + int.domain + "," + int.interaction + "\n";
            }
        }
        else if ($(this).hasClass("effects-download"))
        {
            data = "Domain,InteractionPartner,WT,MT,WTscore,MTscore,DeltaScore,Log2Score,Eval\n";
            for (inter in netdata.mut_effects)
            {
                if ($.inArray(inter, download_proteins) != -1)
                {
                    var effects = netdata.mut_effects[inter];
                    for (eff in effects)
                    {
                        effect = effects[eff];
                        data = data + netdata.domain_info.EnsPID +","+inter+","+effect[0]+","+effect[1]+","+effect[2]+","+effect[3]+","+effect[6]+","+effect[4]+","+effect[5]+"\n";
                    }
                }
            }
        }
        else if ($(this).hasClass("mutation-download"))
        {
            data = "EnsPID,Tissue,Source\n";
            for (mut in netdata.muts)
            {
                var mutation = netdata.muts[mut];
                if ($.inArray(mutation.EnsPID, download_proteins) != -1)
                    data = data + mutation.EnsPID + "," + mutation.Tissue + "," + mutation.Source + "\n";
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
