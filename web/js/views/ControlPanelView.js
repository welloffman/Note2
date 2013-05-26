/**
 * Вид для крошек
 */
var ControlPanelView = Backbone.View.extend({
	initialize: function() {
		this.tagName = 'div';
		this.template = _.template( $('#cpanel').html() );

		this.render = function() {
			$(this.el).html(this.template());
			return this;
		}
	}
});