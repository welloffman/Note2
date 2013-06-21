/**
 * Модель хлебных крошек
 */
var Breadcrumbs = Backbone.Model.extend({
	defaults: function() {
		return {
			items: [],
			cur_dir_title: ''
		};
	},

	initialize: function() {
		var self = this;

		this.sync = function(method, model, options) {
			if(method == 'read') {
				$.post(ROOT + 'get_breadcrumbs', {dir_id: options.dir_id}, function(resp) {
					if(resp.success) {
						self.set('items', resp.items);
						self.set('cur_dir_title', resp.dir_title);
					}
					else alert('Ошибка соединения с сервером!');
				});
			}
		}
	}
});