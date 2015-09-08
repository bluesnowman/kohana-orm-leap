<?php

/**
 * The MIT License
 *
 * Copyright © 2011–2015 Spadefoot Team.
 * Copyright © 2010-2012 Kiall Mac Innes, Mathew Davies, Mike Parkin, and Paul Banks.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Leap\Core\DB\ORM {

	/**
	 * This class represents an active record for an SQL database table and is for handling
	 * a Modified Pre-Order Tree Traversal (MPTT).
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM
	 * @version 2014-07-04
	 *
	 * @see http://imrannazar.com/Modified-Preorder-Tree-Traversal
	 * @see http://www.sitepoint.com/hierarchical-data-database-2/
	 * @see http://iamcam.wordpress.com/2006/03/24/storing-hierarchical-data-in-a-database-part-2a-modified-preorder-tree-traversal/
	 * @see http://www.zaachi.com/en/items/modified-preorder-tree-traversal-algoritmus-1.html
	 * @see http://stackoverflow.com/questions/3344359/database-sorting-hierarchical-data-modified-preorder-tree-traversal-how-to-r
	 * @see http://www.honk.com.au/index.php/2010/04/22/convert-adjacency-list-model-to-a-modified-preorder-tree-traversal-mptt-model-hierarchy/
	 * @see http://stackoverflow.com/questions/7661913/modified-preorder-tree-traversal-selecting-nodes-1-level-deep
	 * @see http://dev.kohanaframework.org/projects/mptt
	 * @see https://github.com/kiall/kohana3-orm_mptt
	 * @see https://github.com/smgladkovskiy/jelly-mptt
	 * @see https://github.com/banks/sprig-mptt
	 */
	abstract class MPTT extends \Leap\Core\DB\ORM\Model {

		/**
		 * This constructor instantiates this class.
		 *
		 * @access public
		 */
		public function __construct() {
			parent::__construct();

			$this->fields = array(
				'id' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'max_length' => 11,
					'nullable' => FALSE,
					'unsigned' => TRUE,
				)),
				'scope' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'max_length' => 11,
					'nullable' => FALSE,
					'unsigned' => TRUE,
				)),
				'name' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'max_length' => 70,
					'nullable' => TRUE,
				)),
				'parent_id' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'max_length' => 11,
					'nullable' => TRUE,
					'unsigned' => TRUE,
				)),
				'lft' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'max_length' => 11,
					'nullable' => FALSE,
					'unsigned' => TRUE,
				)),
				'rgt' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'max_length' => 11,
					'nullable' => FALSE,
					'unsigned' => TRUE,
				)),
			);
		}

		/**
		 * This method returns the value associated with the specified property.
		 *
		 * @access public
		 * @override
		 * @param string $name                                      the name of the property
		 * @return mixed                                            the value of the property
		 * @throws \Leap\Core\Throwable\InvalidProperty\Exception   indicates that the specified property is
		 *                                                          either inaccessible or undefined
		 */
		public function __get($name) {
			switch ($name) {
				case 'ancestors':
					return $this->ancestors('ASC');
				case 'children':
					return $this->children();
				case 'descendants':
					return $this->descendants();
				case 'first_child':
					return $this->children('ASC', 1);
				case 'last_child':
					return $this->children('DESC', 1);
				case 'leaves':
					return $this->leaves();
				case 'level':
					return $this->level();
				case 'parent':
					return $this->parent();
				case 'path':
					return $this->path();
				case 'siblings':
					return $this->siblings();
				case 'root':
					return $this->root();
				default:
					return parent::__get($name);
			}
		}

		/**
		 * This method add a new node as a child to the current node.
		 *
		 * @access public
		 * @param string $name                                      the name to given to the node
		 * @param array $fields                                     an associated array of additional field
		 *                                                          name/value pairs
		 * @return \Leap\Core\DB\ORM\MPTT                           the newly added child node
		 * @throws \Leap\Core\Throwable\Marshalling\Exception       indicates that the node could not
		 *                                                          be added
		 *
		 * @see http://imrannazar.com/Modified-Preorder-Tree-Traversal
		 * @see http://www.sitepoint.com/hierarchical-data-database-3/
		 * @see http://iamcam.wordpress.com/2006/04/14/hierarchical-data-in-a-database2b-modified-preorder-tree-traversal-insertions/
		 */
		public function add_child($name, Array $fields = NULL) {
			if ( ! static::is_savable()) {
				throw new \Leap\Core\Throwable\Marshalling\Exception('Message: Failed to insert record to database. Reason: Model is not insertable.', array(':class' => get_called_class()));
			}

			$data_source = static::data_source(\Leap\Core\DB\DataSource::MASTER_INSTANCE);
			$table = static::table();

			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($data_source);
			$connection->lock->add($table)->acquire();

			$update = \Leap\Core\DB\SQL::update($data_source)
				->set('rgt', \Leap\Core\DB\ORM::expr('rgt + 2'))
				->table($table)
				->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('rgt', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, $this->fields['rgt']->value)
				->command();

			$connection->execute($update);

			$update = \Leap\Core\DB\SQL::update($data_source)
				->set('lft', \Leap\Core\DB\ORM::expr('lft + 2'))
				->table($table)
				->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('lft', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, $this->fields['lft']->value)
				->command();

			$connection->execute($update);

			$lft = $this->fields['lft']->value + 1;
			$rgt = $this->fields['lft']->value + 2;

			$builder = \Leap\Core\DB\SQL::insert($data_source)
				->into($table)
				->column('scope', $this->fields['scope']->value)
				->column('name', $name)
				->column('parent_id', $this->fields['id']->value)
				->column('lft', $lft)
				->column('rgt', $rgt);

			if (is_array($fields)) {
				foreach ($fields as $field => $value) {
					$builder->column($field, $value);
				}
			}

			$insert = $builder->command();

			$connection->execute($insert);
			$id = $connection->get_last_insert_id();

			$select = \Leap\Core\DB\SQL::select($data_source)
				->column('t1.parent_id')
				->from($table, 't1')
				->where('t1.scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('t1.lft', \Leap\Core\DB\SQL\Operator::_LESS_THAN_, \Leap\Core\DB\SQL::expr('t0.lft'))
				->where('t1.rgt', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, \Leap\Core\DB\SQL::expr('t0.rgt'))
				->order_by(\Leap\Core\DB\SQL::expr('t1.rgt - t0.rgt'))
				->limit(1);

			$update = \Leap\Core\DB\SQL::update($data_source)
				->set('t0.parent_id', $select)
				->table($table, 't0')
				->where('t0.scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('t0.lft', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, $this->fields['lft']->value)
				->command();

			$connection->execute($update);

			$connection->lock->release();

			$model = get_class($this);

			$child = new $model();
			$child->id = $id;
			$child->scope = $this->fields['scope']->value;
			$child->name = $name;
			$child->parent_id = $this->fields['id']->value;
			$child->lft = $lft;
			$child->rgt = $rgt;
			if (is_array($fields)) {
				foreach ($fields as $field => $value) {
					$child->{$field} = $value;
				}
			}

			return $child;
		}

		/**
		 * This method returns a result set of ancestor nodes for the current node.
		 *
		 * @access public
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          left column will sorted either in ascending or
		 *                                                          descending order
		 * @param integer $limit                                    the number of ancestors to return
		 * @param boolean $root                                     whether to include the root node
		 * @return \Leap\Core\DB\ResultSet                          a result set of ancestor nodes for the current
		 *                                                          node
		 */
		public function ancestors($ordering = 'ASC', $limit = 0, $root = TRUE) {
			$data_source = static::data_source(\Leap\Core\DB\DataSource::SLAVE_INSTANCE);

			$builder = \Leap\Core\DB\SQL::select($data_source)
				->all('t1.*')
				->from(static::table(), 't1')
				->where('t1.scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('t1.id', \Leap\Core\DB\SQL\Operator::_NOT_EQUIVALENT_, $this->fields['id']->value)
				->where('t1.lft', \Leap\Core\DB\SQL\Operator::_LESS_THAN_, $this->fields['lft']->value)
				->where('t1.rgt', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, $this->fields['rgt']->value)
				->order_by('t1.lft', 'DESC')
				->limit($limit);

			if ( ! $root) {
				$builder->where('t1.lft', \Leap\Core\DB\SQL\Operator::_NOT_EQUAL_TO_, 1);
			}

			if ($ordering == 'ASC') {
				$builder = \Leap\Core\DB\SQL::select($data_source)
					->all('t0.*')
					->from($builder, 't0')
					->order_by('t0.lft', 'ASC');
			}

			return $builder->query(get_class($this));
		}

		/**
		 * This method returns the children of the current node.
		 *
		 * @access public
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          left column will sorted either in ascending or
		 *                                                          descending order
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\ResultSet                          a result set of children nodes
		 */
		public function children($ordering = 'ASC', $limit = 0) {
			return $this->descendants($ordering, $limit, TRUE, FALSE);
		}

		/**
		 * This method removes a node and its descendants.
		 *
		 * @access public
		 * @override
		 * @param boolean $reset                                    whether to reset each column's value back
		 *                                                          to its original value
		 * @throws \Leap\Core\Throwable\Marshalling\Exception       indicates that the record could not be
		 *                                                          deleted
		 *
		 * @see http://imrannazar.com/Modified-Preorder-Tree-Traversal
		 * @see http://stackoverflow.com/questions/1473459/getting-modified-preorder-tree-traversal-data-into-an-array
		 */
		public function delete($reset = FALSE) {
			if ( ! static::is_savable()) {
				throw new \Leap\Core\Throwable\Marshalling\Exception('Message: Failed to delete record from database. Reason: Model is not savable.', array(':class' => get_called_class()));
			}

			$data_source = static::data_source(\Leap\Core\DB\DataSource::MASTER_INSTANCE);
			$table = static::table();

			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($data_source);
			$connection->lock->add($table)->acquire();

			$select = \Leap\Core\DB\SQL::select($data_source)
				->column('t1.parent_id')
				->from($table, 't1')
				->where('t1.scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('t1.id', \Leap\Core\DB\SQL\Operator::_NOT_EQUIVALENT_, $this->fields['id']->value)
				->where('t1.lft', \Leap\Core\DB\SQL\Operator::_LESS_THAN_, \Leap\Core\DB\SQL::expr('t0.lft'))
				->where('t1.rgt', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, \Leap\Core\DB\SQL::expr('t0.rgt'))
				->order_by(\Leap\Core\DB\SQL::expr('t1.rgt - t0.rgt'))
				->limit(1);

			$update = \Leap\Core\DB\SQL::update($data_source)
				->set('t0.parent_id', $select)
				->set('t0.lft', \Leap\Core\DB\ORM::expr('t0.lft - 2'))
				->set('t0.rgt', \Leap\Core\DB\ORM::expr('t0.rgt - 2'))
				->table($table, 't0')
				->where('t0.scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('t0.lft', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, $this->fields['rgt']->value)
				->command();

			$connection->execute($update);

			$delete = \Leap\Core\DB\SQL::delete($data_source)
				->from($table)
				->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('id', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['id']->value)
				->command();

			$connection->execute($delete);

			$connection->lock->release();

			if ($reset) {
				$this->reset();
			}
			else {
				$this->metadata['saved'] = NULL;
			}
		}

		/**
		 * This method returns the descendants of the current node.
		 *
		 * @access public
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          left column will sorted either in ascending or
		 *                                                          descending order
		 * @param integer $limit                                    the "limit" constraint
		 * @param boolean $children_only                            whether to only fetch the direct children
		 * @param boolean $leaves_only                              whether to only fetch leaves
		 * @return \Leap\Core\DB\ResultSet                          a result set of descendant nodes
		 */
		public function descendants($ordering = 'ASC', $limit = 0, $children_only = FALSE, $leaves_only = FALSE) {
			$builder = \Leap\Core\DB\ORM::select(get_class($this))
				->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('lft', \Leap\Core\DB\SQL\Operator::_GREATER_THAN_, $this->fields['lft']->value)
				->where('rgt', \Leap\Core\DB\SQL\Operator::_LESS_THAN_, $this->fields['rgt']->value)
				->order_by('lft', $ordering)
				->limit($limit);

			if ($children_only) {
				$builder->where('parent_id', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['id']->value);
			}

			if ($leaves_only) {
				$builder->where('rgt', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, \Leap\Core\DB\SQL::expr('lft + 1'));
			}

			return $builder->query();
		}

		/**
		 * This method determines whether the current node has children.
		 *
		 * @access public
		 * @return boolean                                          whether the current node has children
		 */
		public function has_children() {
			return (($this->fields['rgt']->value - $this->fields['lft']->value) > 1);
		}

		/**
		 * This method determines whether the current node is an ancestor of the
		 * supplied node.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\MPTT $descendant                the descendant node
		 * @return boolean                                          whether the current node is an ancestor
		 *                                                          of the supplied node
		 */
		public function is_ancestor(\Leap\Core\DB\ORM\MPTT $descendant) {
			return (($descendant->scope == $this->fields['scope']->value) AND ($descendant->lft > $this->fields['lft']->value) AND ($descendant->rgt < $this->fields['rgt']->value));
		}

		/**
		 * This method determines whether the current node is the child of the supplied
		 * node.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\MPTT $parent                    the parent node
		 * @return boolean                                          whether the current node is the parent
		 *                                                          of the supplied node
		 */
		public function is_child(\Leap\Core\DB\ORM\MPTT $parent) {
			return (($parent->scope == $this->fields['scope']->value) AND ($parent->id == $this->fields['parent_id']->value));
		}

		/**
		 * This method determines whether the current node is a descendant of the
		 * supplied node.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\MPTT $ancestor                  the ancestor node
		 * @return boolean                                          whether the current node is a descendant
		 *                                                          of the supplied node
		 */
		public function is_descendant(\Leap\Core\DB\ORM\MPTT $ancestor) {
			return (($ancestor->scope == $this->fields['scope']->value) AND ($ancestor->lft < $this->fields['lft']->value) AND ($ancestor->rgt > $this->fields['rgt']->value));
		}

		/**
		 * This method determines whether the current node is a leaf.
		 *
		 * @access public
		 * @return boolean                                          whether the current node is a leaf
		 */
		public function is_leaf() {
			return ! $this->has_children();
		}

		/**
		 * This method determines whether the current node is the parent of the supplied
		 * node.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\MPTT $child                     the child node
		 * @return boolean                                          whether the supplied node is a child
		 *                                                          of the current node
		 */
		public function is_parent(\Leap\Core\DB\ORM\MPTT $child) {
			return (($child->scope == $this->fields['scope']->value) AND ($child->parent_id === $this->fields['id']->value));
		}

		/**
		 * This method determines whether the current node is the root.
		 *
		 * @access public
		 * @return boolean                                          whether the current node is the root
		 */
		public function is_root() {
			return ($this->fields['lft']->value == 1);
		}

		/**
		 * This method determines whether the current node is a sibling of the supplied
		 * node.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\MPTT $sibling                   a sibling node
		 * @return boolean                                          whether the current node is a sibling
		 *                                                          of the supplied node
		 */
		public function is_sibling(\Leap\Core\DB\ORM\MPTT $sibling) {
			return (($sibling->scope == $this->fields['scope']->value) AND ($sibling->parent_id == $this->fields['parent_id']->value) AND ($sibling->id != $this->fields['id']->value));
		}

		/**
		 * This method returns all leaves under the current node.
		 *
		 * @access public
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          left column will sorted either in ascending or
		 *                                                          descending order
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\ResultSet                          the leaves under the current node
		 */
		public function leaves($ordering = 'ASC', $limit = 0) {
			return $this->descendants($ordering, $limit, FALSE, TRUE);
		}

		/**
		 * This method returns the level (i.e. depth) at which the current node resides.
		 *
		 * @access public
		 * @return integer                                          the level at which the current
		 *                                                          node resides
		 *
		 * @see http://stackoverflow.com/questions/7661913/modified-preorder-tree-traversal-selecting-nodes-1-level-deep
		 */
		public function level() {
			$record = \Leap\Core\DB\SQL::select(static::data_source(\Leap\Core\DB\DataSource::SLAVE_INSTANCE))
				->column(\Leap\Core\DB\SQL::expr('COUNT(parent_id) - 1'), 'level')
				->from(static::table())
				->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('parent_id', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['parent_id']->value)
				->group_by('parent_id')
				->query()
				->fetch();

			return ($record) ? $record['level'] : 0;
		}

		/**
		 * This method returns a model describing the parent node.
		 *
		 * @access public
		 * @return \Leap\Core\DB\ORM\MPTT                           a model describing the parent node
		 */
		public function parent() {
			if ( ! $this->is_root()) {
				return $this->ancestors('DESC', 1, TRUE)->fetch(0);
			}
			return FALSE;
		}

		/**
		 * This method returns the path to the current node.
		 *
		 * @access public
		 * @return array                                            the path to the current node
		 *
		 * @see http://www.sitepoint.com/hierarchical-data-database/
		 */
		public function path() {
			$path = array();

			foreach ($this->ancestors() as $ancestor) {
				$path[] = $ancestor->id;
			}

			$path[] = $this->fields['id']->value;

			return $path;
		}

		/**
		 * This method returns a model describing the root node.
		 *
		 * @access public
		 * @return \Leap\Core\DB\ORM\MPTT                           a model describing the root node
		 */
		public function root() {
			$record = \Leap\Core\DB\ORM::select(get_class($this))
				->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
				->where('lft', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, 1)
				->limit(1)
				->query()
				->fetch();

			return $record;
		}

		/**
		 * This method saves the record matching using the primary key.
		 *
		 * @access public
		 * @override
		 * @param boolean $reload                                   whether the model should be reloaded
		 *                                                          after the save is done
		 * @param boolean $mode                                     TRUE=save, FALSE=update, NULL=automatic
		 * @throws \Leap\Core\Throwable\Marshalling\Exception       indicates that model could not be saved
		 */
		public function save($reload = FALSE, $mode = NULL) {
			if ( ! static::is_savable()) {
				throw new \Leap\Core\Throwable\Marshalling\Exception('Message: Failed to save record to database. Reason: Model is not savable.', array(':class' => get_called_class()));
			}

			$columns = array_keys($this->fields);

			if ( ! empty($columns)) {
				$builder = \Leap\Core\DB\SQL::update(static::data_source(\Leap\Core\DB\DataSource::MASTER_INSTANCE))
					->table(static::table())
					->where('id', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['id']->value);

				$ignore_keys = array('id', 'scope', 'parent_id', 'lft', 'rgt');

				// Is there any data to save and it's worth to execute the query?
				$is_worth = FALSE;

				foreach ($columns as $column) {
					if ($this->fields[$column]->savable AND $this->fields[$column]->modified AND ! in_array($column, $ignore_keys)) {
						// Add column values to the query builder
						$builder->set($column, $this->fields[$column]->value);

						// It's worth do execute the query.
						$is_worth = TRUE;
					}

					// Mark field as not modified
					$this->fields[$column]->modified = FALSE;
				}

				// Execute the query only if there is data to save
				if ($is_worth) {
					$builder->execute();
				}

				$this->metadata['saved'] = $this->hash_code();
			}

			if ($reload) {
				$this->load();
			}
		}

		/**
		 * This method returns the siblings of the current node.
		 *
		 * @access public
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          left column will sorted either in ascending or
		 *                                                          descending order
		 * @param boolean $self                                     whether to include the current node
		 * @return \Leap\Core\DB\ResultSet                          an array of sibling nodes
		 */
		public function siblings($ordering = 'ASC', $self = FALSE) {
			if ( ! $this->root()) {
				$builder = \Leap\Core\DB\ORM::select(get_class($this))
					->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['scope']->value)
					->where('parent_id', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->fields['parent_id']->value)
					->order_by('lft', $ordering);

				if ( ! $self) {
					$builder->where('id', \Leap\Core\DB\SQL\Operator::_NOT_EQUIVALENT_, $this->fields['id']->value);
				}

				return $builder->query();
			}

			$results = new \Leap\Core\DB\ResultSet(array());

			return $results;
		}

		/**
		 * This method returns the size of the current node, which is the number of descendants
		 * it has including itself.
		 *
		 * @access public
		 * @return integer                                          the size of the current node
		 *
		 * @see http://iamcam.wordpress.com/2006/03/24/storing-hierarchical-data-in-a-database-part-2a-modified-preorder-tree-traversal/
		 */
		public function size() {
			return ((($this->fields['rgt']->value - $this->fields['lft']->value) - 1) / 2) + 1;
		}

		/**
		 * This method returns a tree of nodes, where the root node of the tree is the current
		 * node.
		 *
		 * @access public
		 * @return array                                            the tree as a multidimensional array
		 */
		public function tree() {
			$tree = array();
			$tree['node'] = $this->as_array();
			foreach ($this->children() as $child) {
				$tree['node']['children'][] = $child->tree();
			}
			return $tree;
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * This method creates a new root node in the specified scope.
		 *
		 * @access public
		 * @static
		 * @param integer $scope                                    the new scope to be create
		 * @param string $name                                      the name to given to the node
		 * @param array $fields                                     an associated array of additional field
		 *                                                          name/value pairs
		 * @return \Leap\Core\DB\ORM\MPTT                           the newly created root node
		 **/
		public static function add_root($scope, $name, Array $fields = NULL) {
			$data_source = static::data_source(\Leap\Core\DB\DataSource::MASTER_INSTANCE);
			$table = static::table();

			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($data_source);
			$connection->lock->add($table)->acquire();

			$builder = \Leap\Core\DB\SQL::insert($data_source)
				->into($table)
				->column('scope', $scope)
				->column('name', $name)
				->column('parent_id', NULL)
				->column('lft', 1)
				->column('rgt', 2);

			if (is_array($fields)) {
				foreach ($fields as $field => $value) {
					$builder->column($field, $value);
				}
			}

			$insert = $builder->command();

			$connection->execute($insert);
			$id = $connection->get_last_insert_id();

			$connection->lock->release();

			$model = get_called_class();

			$root = new $model();
			$root->id = $id;
			$root->scope = $scope;
			$root->name = $name;
			$root->parent_id = NULL;
			$root->lft = 1;
			$root->rgt = 2;
			if (is_array($fields)) {
				foreach ($fields as $field => $value) {
					$root->{$field} = $value;
				}
			}

			return $root;
		}

		/**
		 * This method returns a result set containing all nodes in the specified tree's scope.
		 *
		 * @access public
		 * @static
		 * @param integer $scope                                    the scope of the desired tree
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          left column will sorted either in ascending or
		 *                                                          descending order
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\ResultSet                          a result set containing all nodes in the
		 *                                                          specified tree's scope
		 */
		public static function full_tree($scope, $ordering = 'ASC', $limit = 0) {
			$model = get_called_class();

			$results = \Leap\Core\DB\ORM::select($model)
				->where('scope', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $scope)
				->order_by('lft', $ordering)
				->limit($limit)
				->query();

			return $results;
		}


		/**
		 * This method returns the primary key for the database table.
		 *
		 * @access public
		 * @static
		 * @return array                                            the primary key
		 */
		public static function primary_key() {
			return array('id');
		}

		/**
		 * This method returns the database table's name.
		 *
		 * @access public
		 * @static
		 * @return string                                           the database table's name
		 */
		public static function table() {
			return 'mptt';
		}

	}

}