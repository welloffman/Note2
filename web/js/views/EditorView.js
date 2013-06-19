/**
 * Вид для Редактора
 */
var EditorView = Backbone.View.extend({
	events: {
		'click .js-save': 'save',
		'click .js-cancel': 'cancel'
	},

	save: function() {
		tinyMCE.activeEditor.save();
		if(this.model.get("type") == "note") {
			this.model.get("entity").updateModel({title: $(this.el).find('.js-title').val(), content: $("#mce").val()});
		} else {
			this.model.get("entity").updateModel({title: $(this.el).find('.js-title').val()});
		}
		this.model.get("entity").save();
		$(this.el).find('.js-cancel').trigger('click');
	},

	cancel: function() {
		tinyMCE.execCommand('mceRemoveControl', false, "mce");
		this.remove();
		var cur_dir = this.model.get('entity').get('parent_dir');
		if(cur_dir) location.hash = "dir/" + cur_dir;
		else location.hash = "";
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