/**
 * Модель для раздела
 */
var Dir = Backbone.Model.extend({
	
	defaults: function() {
		return {
			id: undefined,
			title: undefined,
			pid: undefined
		};
	},

	initialize: function() {
		var self = this;

		this.sync = function(method, model, options) {
			if(method == 'read') {
				
			}
			else if(method == 'save') {
				
			}
			else if(method == 'create') {
				$.post(ROOT + "save_dir", {dir_data: self.toJSON()}, function(resp) {
					if(resp.success) options.success();
					else alert('Не удалось создать раздел');
				});
			}
		}
	},

});