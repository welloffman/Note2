/**
 * Вид для Редактора
 */
var EditorView = Backbone.View.extend({
	events: {
		'click .js-save': 'save',
		'click .js-cancel': 'cancel'
	},

	save: function() {
		var self = this;
		if(this.model.get("type") == "note") {
			tinyMCE.activeEditor.save();
			var new_data = {title: $(this.el).find('.js-title').val(), content: $("#mce").val()};
		} else {
			new_data = {title: $(this.el).find('.js-title').val()};
		}
		var cancel_button = $(this.el).find('.js-cancel');
		this.model.get("entity").save(new_data, {silent: true, callback: function() {
			self.cancel();
		}});
		
	},

	cancel: function() {
		tinyMCE.execCommand('mceRemoveControl', false, "mce");
		this.remove();
		$("body").trigger({type: "refresh", item: this.model});
	},

	initialize: function() {
		var self = this;

		this.tagName = 'div';
		this.template = _.template( $('#editor').html() );

		this.render = function() {
			this.remove();
			$(this.el).html(this.template( {item: this.model.toJSON()} ));
			this.delegateEvents();
			return this;
		}
	}
});