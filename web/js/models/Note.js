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
					if(resp.success) {
						self.set({title: ''}, {silent: true}); // Иначе не срабатывает событие change если модель не изменилась
						self.set( _.extend(self.attributes, resp.note) );
						if(typeof options.callback == "function") options.callback();
					}
					else alert('Не удалось получить запись');
				});
			}
			else if(method == 'create' || method == 'update') {
				$.post(ROOT + "save_note", {note_data: self.toJSON()}, function(resp) {
					if(resp.success) {
						if(typeof options.callback == "function") options.callback();
					}
					else alert('Не удалось создать запись');
				});
			}
		}

		this.forceChange = function() {
			var tmp = self.get('title');
			self.set({title: ''}, {silent: true});
			self.set({title: tmp});
		}
	},

});