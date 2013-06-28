/**
 * Модель для копирования, вставки, вырезания
 */
var CopyPaste = Backbone.Model.extend({
	
	defaults: function() {
		return {
			target_dir: undefined,
			action_type: undefined,
			items: []
		};
	},

	initialize: function() {
		var self = this;

		this.sync = function(method, model, options) {
			if(method == 'create' || method == 'update') {
				var items = self.get('items').map(function(item) {
					return {'id': item.get('entity').get('id'), 'type': item.get('type')};
				});
				if(items.length) {
					$.post(ROOT + "paste", { action_type: self.get('action_type'), target_dir: self.get('target_dir'), items: items }, function(resp) {
						if(resp.success) {
							if(typeof options.callback == 'function') options.callback();
						} else {
							var error_str = '';
							if(resp.errors.length) _.each(resp.errors, function(item) { error_str += item + '<br />'; });
							else error_str = 'Не удалось произвести вставку';
							var popup = new PopupView({
								model: new Popup({
									title: 'Внимание!', 
									content: error_str,
									type: 'alert'
								}),
								className: 'popup'
							});
							$("body").append(popup.render().el);
						}
						clear();
					});
				}
			}
		}

		function clear() {
			self.set({
				target_dir: undefined,
				action_type: undefined,
				items: []
			});
		}
	},

});