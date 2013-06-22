/**
 * Вид для контрольной панели
 */
var ControlPanelView = Backbone.View.extend({
	events: {
		'click .cp-delete': function() { $('body').trigger('cp-delete'); return false; },
		'click .cp-edit': function() { $('body').trigger('cp-edit'); return false; }
	},

	initialize: function() {
		var self = this;

		this.tagName = 'div';
		this.template = _.template( $('#cpanel').html() );

		this.render = function() {
			this.remove();
			$(this.el).html(this.template( {model: this.model.toJSON()} ));
			this.delegateEvents();
			return this;
		}

		this.model.bind('change', function() {
			$(".js-control-panel").html(self.render().el);
		});
	}
});