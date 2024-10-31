(function() {
	tinymce.PluginManager.add('npteam_tinymce_btn', function( editor, url ) {
		editor.addButton('npteam_tinymce_btn', {
			icon: 'team-mce-icon',
			onclick: function() {
				editor.windowManager.open( {
					title: 'NP Team',
					body: [					
						{
							type: 'textbox',
							name: 'number',
							label: 'Number of team members (blank means unlimlied)',
							value: '6'
						},		


					],
					onsubmit: function( e ) {
						editor.insertContent( '[team number="'+ e.data.number +'"][/team]');
					}
				});
			}
		});
	});
})();