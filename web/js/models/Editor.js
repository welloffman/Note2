/**
 * Модель для редактора
 */
var Editor = Backbone.Model.extend({
	
	defaults: function() {
		return {
			type: undefined,
			entity: undefined
		};
	},

	initialize: function() {
		var self = this;
	}
});