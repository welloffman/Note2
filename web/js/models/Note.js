/**
 * Модель для записи
 */
var Note = Backbone.Model.extend({
	
	defaults: function() {
		return {
			id: undefined,
			title: undefined,
			content: undefined,
			pid: undefined
		};
	},

	initialize: function() {
		var self = this;

		this.sync = function(method, model, options) {
			if(method == 'read') {
				$.post(ROOT + 'get_note', {note_id: self.get('id')}, function(resp) {
					if(resp.success) self.set( _.extend(self.attributes, resp.note) );
					else alert('Не удалось получить запись');
				});
			}
			else if(method == 'save') {
				
			}
			else if(method == 'create') {
				$.post(ROOT + "save_note", {note_data: self.toJSON()}, function(resp) {
					if(resp.success) options.success();
					else alert('Не удалось создать запись');
				});
			}
		}
	},

});