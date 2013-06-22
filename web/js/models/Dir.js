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
				$.post(ROOT + 'get_dir', {dir_id: self.get('id')}, function(resp) {
					if(resp.success) {
						self.set({title: ''}, {silent: true}); // Иначе не срабатывает событие change если модель не изменилась
						self.set( _.extend(self.attributes, resp.dir) );
						if(typeof options.callback == "function") options.callback();
					}
					else alert('Не удалось получить раздел');
				});
			}
			else if(method == 'create' || method == 'update') {
				$.post(ROOT + "save_dir", {dir_data: self.toJSON()}, function(resp) {
					if(resp.success) options.success();
					else alert('Не удалось создать раздел');
				});
			}
		}
	},

});