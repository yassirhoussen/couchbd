<?php
	interface IDao {
		
		public function getAll();
		
		public function getOne($id);
		
		public function create($object);
		
		public function update($object);
		
		
	}