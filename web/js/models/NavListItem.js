/**
 * Модель для элемента списка разделов и записей
 */
var NavListItem = Backbone.Model.extend({

	defaults: function() {
		return {
			type: undefined,
			entity: undefined,
			selected: false
		};
	},

	initialize: function() {
		var self = this;

		this.is_changed = false;

		this.getPos = function() {
			var position = self.get('entity').get('position');
			return position.pos;
		}

		this.setPos = function(pos) {
			var new_position = self.get('entity').get('position');
			new_position.pos = pos;
			self.get('entity').set({position: new_position});
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
					if(el.is_changed) items.push( _.extend(el.get('entity').toJSON(), {'type': el.get('type')}) );
				});

				if(items.length > 0) {
					$.post(ROOT + 'save_nav_list', {items: items}, function(resp) {
						if(resp.success) clearChangeHistory();
						else alert('Ошибка соединения с сервером!');
					});
				}
			} else if(method == 'read') {
				$.post(ROOT + 'get_nav_list', {dir_id: options.dir_id}, function(resp) {
					if(resp.success) {
						var data = _.map(resp.items, function(item) {
							if(item.type == 'dir') item.entity = new Dir(item.entity);
							else if(item.type == 'note') item.entity = new Note(item.entity);
							return item;
						});
						self.reset(data, {dir_id: resp.dir_id});
						if(typeof options.callback == "function") options.callback();
					} else {
						alert('Ошибка соединения с сервером!');
					}
				});
			} else if(method == 'delete') {
				var selected = self.where({'selected': true});
				var data = { dir: [], note: [] };
				_.each(selected, function(item) {
					data[ item.get('type') ].push(item.get('entity').get('id'));
				});
				if(data.dir.length > 0 || data.note.length > 0) {
					$.post(ROOT + 'delete', {ids: data}, function(resp) {
						if(resp.success) {
							self.remove(selected);
							if(typeof options.callback == "function") options.callback();
						}
						else alert('Удалить данные не удалось');
					});
				}
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

				var model = self.getByEntity(elem.attr("class"), elem.data("id"));
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
			var model = self.getByEntity(type, id);
			if(model) model.set('selected', !model.get('selected'));
		};

		/**
		 * Возвращает NavListItem по id сущности и типу
		 * @param string type
		 * @param int id
		 * @return NavListItem | null
		 */
		this.getByEntity = function(type, id) {
			var items = self.where({type: type});
			for(var i in items) {
				if(items[i].get('entity').get('id') == id) return items[i]; 
			}
			return null;
		}

		this.getFirstSelected = function() {
			var selected = self.where({'selected': true});
			return selected.length ? selected[0] : null;
		}

		function clearChangeHistory() {
			self.each(function(item) {
				item.is_changed = false;
			});
		}
	}
});