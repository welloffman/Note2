/**
 * Модель для записи
 */
var Note = Backbone.Model.extend({
	
	defaults: function() {
		return {
			id: undefined,
			title: undefined,
			content: undefined
		};
	},

	initialize: function() {
		var self = this;

		this.updateModel = function(data) {
			if(!data) return false;

			self.set({
				'id': data.id,
				'title': data.title,
				'content': data.content
			});
		}

		this.sync = function(method, model, options) {
			if(method == 'read') {
				$.post('./get_note', {note_id: self.get('id')}, function(resp) {
					resp = $.parseJSON(resp);
					if(resp.success) self.updateModel(resp.note);
					else alert('Ошибка соединения с сервером!');
				});
			}
		}
	},

});