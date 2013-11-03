pimcore.registerNS('pimcore.plugin.soundcloud');
pimcore.plugin.soundcloud = Class.create(pimcore.plugin.admin, {
	
	assetObj : null,
	
	getClassName: function (){
		return "pimcore.plugin.soundcloud";
	},	
	initialize: function(){
		pimcore.plugin.broker.registerPlugin(this);
	},
	/**
	 * Adds a new button to the tablayout,
	 * but only if asset is mp3|wav|aif
	 * 
	 * @param asset (pimcore.asset)
	 * @param type (string)
	 */
	postOpenAsset : function(asset, type) {
		var matched = asset.data.filename.match(/(\.mp3)|(\.wav)|(\.aif)$/);
		if(matched) {			
			this.assetObj = asset;

			this.assetObj.toolbar.add(new Ext.Button({
				'text' : 'Soundcloud',
				'iconCls' : 'soundcloud-button-icon',
				'handler' : this.initBackendController.bind(this)
			}));
		}
		//console.log('toolbar', this.assetObj.toolbar);
	},
	initBackendController : function() {
		 Ext.Ajax.request({
            url: "/plugin/Soundcloud/backend/index",
            success: this.openWindow.bind(this),
            params: {
                id: this.assetObj.id
            }
        });
	},
	openWindow : function(data) {

		var fileInfo = Ext.decode(data.responseText).success.fileInfo;
		var userInfo = Ext.decode(data.responseText).success.userInfo;
		var assetId = this.assetObj.id;
		
		var tabs = new Ext.TabPanel({
			activeTab : 0,
			cls : 'soundcloud-window',
			items : [
				{
					title	: 'Transfer ' + fileInfo.fileName,
					//html	: this.getFileInfo(fileInfo),
					layout	: 'fit',
					items	: new Ext.form.FormPanel({
						id		: 'soundcloud-file-form',
						url		: '/plugin/Soundcloud/backend/submit-track/',
						baseParams	: {id : assetId},
						//method	: 'post',
						frame	: true,
						buttons : [
							{
								text : 'Submit',
								handler : function() {
									var form = Ext.getCmp('soundcloud-file-form').getForm();

									form.submit({
										success : function(response) {
											console.log(response);
										}
									});
								}
							}
						],
						items : [
							new Ext.form.FieldSet({
								title : 'File Information',
								autoHeight : true,
								defaultType : 'textfield',
								items : [
									{
										fieldLabel	: 'Title',
										emptyText	: 'Title',
										name		: 'title',
										width		: 200
									},
									{
										fieldLabel	: 'Permalink',
										emptyText	: 'Permalink',
										name		: 'permalink',
										width		: 200
									},
									{
										fieldLabel	: 'Comment',
										emptyText	: 'Add your comment here',
										name		: 'comment',
										width		: 200
									},
								]
							})
						]
					}),
					id		: 'soundcloud-file-transfer'
				},
//				{
//					'title' : 'Delete track',
//					html	: '<div>Track goes here</div>'
//				},
				{
					title	: 'My Info',
					html    : this.getUserInfo(userInfo),
					id		: 'soundcloud-user-info'
				}
			]
		});
		

		this.window = new Ext.Window({
            layout:'fit',
            width:500,
            height:300,
            closeAction:'close',
            modal: true
        });
		this.window.add(tabs);
        pimcoreViewport.add(this.window);

        this.window.show();
		
//		var button = new Ext.Button({
//			text : 'Submit',
//			applyTo : 'button-div',
//			handler : this.submit.bind(this)
//		});
	},
	/**
	 *
	 */
	getUserInfo : function(userInfo) {
		var tpl = new Ext.XTemplate(
			'<h2>Your user data on Soundcloud</h2>',
			'<table style="width:100%">',
			'<tr><td>City</td><td>{city}</td></tr>',
			'<tr><td>Country</td><td>{country}</td></tr>',
			'</table>'
		);
		tpl.compile();
		return tpl.apply(userInfo);
	},
	/**
	 * 
	 */
	getFileInfo : function(fileInfo) {
		
		var tpl = new Ext.XTemplate(
			'<div id="soundcloud-file-info">',
			'<h2>Fileinfo</h2>',
			'<table style="width:100%">',
			'<tr><td>Path</td><td>{fullPath}</td></tr>',
			'<tr><td>Size</td><td>{fileSize}</td></tr>',
			'</table>',
			'</div>',
			'<div id="button-div"></div>'
		);
		tpl.compile();
				
		return tpl.apply(fileInfo);
	}
});
/* comes with next version */
//new pimcore.plugin.soundcloud();