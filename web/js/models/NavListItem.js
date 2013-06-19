/**
 * Модель для элемента списка разделов и записей
 */
var NavListItem = Backbone.Model.extend({
	
	defaults: function() {
		return {
			id: undefined,
			title: undefined,
			type: undefined,
			position: undefined,
			selected: false
		};
	},

	initialize: function() {
		var self = this;

		this.is_changed = false;

		this.getPos = function() {
			var position = self.get('position');
			return position.pos;
		}

		this.setPos = function(pos) {
			var new_position = self.get('position');
			new_position.pos = pos;
			self.set({position: new_position});
		}
	}
});

/**
 * Коллекция для элемента списка разделов и записей
 */
var NavList = Backbone.Collection.extend({
	model: NavListItem,

	initialize: function() {
		var self = this;

		this.sync = function(method, collection, options) {
			if(method == 'save') {
				var items = [];
				self.each(function(el) {
					if(el.is_changed) items.push(el.toJSON());
				});

				if(items.length > 0) {
					$.post('./save_nav_list', {items: items}, function(resp) {
						resp = $.parseJSON(resp);
						if(resp.success) clearChangeHistory();
						else alert('Ошибка соединения с сервером!');
					});
				}
			} else if(method == 'read') {
				$.post('./get_nav_list', {dir_id: options.dir_id}, function(resp) {
					resp = $.parseJSON(resp);
					if(resp.success) self.reset(resp.items, {dir_id: resp.dir_id});
					else alert('Ошибка соединения с сервером!');
				});
			} else if(method == 'delete') {
				var selected = self.where({'selected': true});
				var data = { dir: [], note: [] };
				_.each(selected, function(item) {
					data[ item.get('type') ].push(item.get('id'));
				});
				$.post('./delete', {ids: data}, function(resp) {
					resp = $.parseJSON(resp);
					if(resp.success) self.remove(selected);
					else alert('Ошибка соединения с сервером!');
				});
			}
		};

		/**
		 * Сохраняет на сервер позиции для элементов коллекции с изменной позицией
		 * @param  Event e Объект события
		 * @param  JqueryObject ui Элемент переместившегося блока (от jquery sortable)
		 */
		this.savePositions = function(e, ui) { 
			$.each(ui.item.parent().children(), function(i, el){
				var elem = $(el);

				var model = self.where({type: elem.attr("class"), id: elem.data("id")})[0];
				if(model.getPos() != i) {
					model.setPos(i);
					model.is_changed = true;
				}
			});

			self.sync('save');
		};

		/**
		 * Обработка выбора пункта списка
		 * @param  string type Тип пункта
		 * @param  int id Ид пункта
		 */
		this.toggle = function(type, id) {
			var array = self.where({'type': type, 'id': id});
			var model = array.length ? array[0] : null;
			if(model) {
				model.set('selected', !model.get('selected'));
			}
		};

		function clearChangeHistory() {
			self.each(function(item) {
				item.is_changed = false;
			});
		}
	}
});