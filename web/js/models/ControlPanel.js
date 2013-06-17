/**
 * Модель для контрольной панели
 */
var ControlPanel = Backbone.Model.extend({
	
	defaults: function() {
		return {
			cur_dir_id: undefined,
		};
	},

	initialize: function() {
		var self = this;
	}
});