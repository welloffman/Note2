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
				$.post('./get_breadcrumbs', {dir_id: options.dir_id}, function(resp) {
					resp = $.parseJSON(resp);
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