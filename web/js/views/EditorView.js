/**
 * Вид для Редактора
 */
var EditorView = Backbone.View.extend({
	initialize: function() {
		var self = this;

		this.tagName = 'div';
		this.template = _.template( $('#editor').html() );

		this.render = function() {
			$(this.el).html(this.template( {item: this.model.toJSON()} ));
			bindEventes();
			return this;
		}

		function bindEventes() {
			$(self.el).find('.js-save').on('click', function() {
				tinyMCE.activeEditor.save();
				if(self.model.get("type") == "note") {
					self.model.get("entity").updateModel({title: $(self.el).find('.js-title').val(), content: $("#mce").val()});
				} else {
					self.model.get("entity").updateModel({title: $(self.el).find('.js-title').val()});
				}
				self.model.get("entity").save();
				$(self.el).find('.js-cancel').trigger('click');
			});

			$(self.el).find('.js-cancel').on('click', function() {
				tinyMCE.execCommand('mceRemoveControl', false, "mce");
				self.remove();
				var cur_dir = self.model.get('entity').get('parent_dir');
				if(cur_dir) location.hash = "dir/" + cur_dir;
				else location.hash = "";
			});
		}
	}
});