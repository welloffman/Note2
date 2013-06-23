/**
 * Модель для всплывающего окна
 */
var Popup = Backbone.Model.extend({
	
	defaults: function() {
		return {
			title: undefined,
			content: undefined,
			ok_callback: undefined
		};
	}
});