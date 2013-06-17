/**
 * Вид для контрольной панели
 */
var ControlPanelView = Backbone.View.extend({
	initialize: function() {
		var self = this;

		this.tagName = 'div';
		this.template = _.template( $('#cpanel').html() );

		this.render = function() {
			$(this.el).html(this.template( {model: this.model.toJSON()} ));
			bindEventes();
			return this;
		}

		function bindEventes() {
			
		}

		this.model.bind('change', function() {
			$(".js-control-panel").html(self.render().el);
		});
	}
});