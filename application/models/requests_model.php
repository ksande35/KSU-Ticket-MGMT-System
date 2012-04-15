<?php

class Requests_model extends CI_Model {
	
	//function adds request to database, uses scheduling algorithm to choose technician
	function create_request($reporterID, $description, $location) {
		//
		//Insert function that handles scheduling algorithm
		//
		$techID = 6; //Placeholder
		
		$this->load->helper('date');
		$datestring = "Year: %Y Month: %m Day: %d - %h:%i %a";
		$time = time();
		
		$submissionDate = mdate($datestring, $time);
		
		$requests_data = array(
			'tech' => $techID,
			'reporter' => $reporterID,
			'description' => $description,
			'location' => $location,
			'submissionDate' => $submissionDate,
			'isRepaired' => 1
		);
		$insert = $this->db->insert('Requests', $requests_data);
		return $insert;
	}
	

	function update($key, $id, $field, $value){
		$this->db->where($key, $id);
		$this->db->update('Requests', array($field=>$value));
	}

	//Search used by Tablebuilder class to pull data for paginated tables
	function search($limit, $offset, $sort_by, $sort_order, $select_user) {
		
		$sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
		
		$sort_columns = array('report_id', 'tech', 'reporter', 'description', 'location', 'submissionDate', 'completionDate', 'feedback', 'isRepaired');
		$sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : "report_id";
		
		// results query
		if ($select_user==NULL)
			$q = $this->db->select('report_id, tech, reporter, description, location, submissionDate, completionDate, feedback, isRepaired')
				->from('Requests')
				->limit($limit, $offset)
				->order_by($sort_by, $sort_order);
		else
			$q = $this->db->select('report_id, tech, reporter, description, location, submissionDate, completionDate, feedback, isRepaired')
				->from('Requests')
				->where('tech', $select_user)
				->or_where('reporter', $select_user)
				->limit($limit, $offset)
				->order_by($sort_by, $sort_order);
	
		$ret['rows'] = $q->get()->result();
		
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from('Requests');
		
		$tmp = $q->get()->result();
		
		$ret['num_rows'] = $tmp[0]->count;
		
		return $ret;
	}
	
	function display_fields() {
	
		$fields = array(
			'report_id'=> 'Request #',
			'tech'=> 'Technician',
			'reporter'=> 'User',
			'description'=> 'Description',
			'location'=> 'Location',
			'submissionDate'=> 'Submission Date (M/D/Y)',
			'completionDate'=> 'Completion Date (M/D/Y)',
			'feedback'=> 'Feedback',
			'isRepaired'=> 'Completed?'
		);
		return $fields;
	}
	
	function editable_fields($role){
		if ($role == 'admin'){
		$fields = array(
				'report_id'=> FALSE,
				'tech'=> TRUE,
				'reporter'=> FALSE,
				'description'=> TRUE,
				'location'=> TRUE,
				'submissionDate'=> FALSE,
				'completionDate'=> FALSE,
				'feedback'=> TRUE,
				'isRepaired'=> TRUE
			);
		}else if ($role == 'tech'){
			$fields = array(
				'report_id'=> FALSE,
				'tech'=> FALSE,
				'reporter'=> FALSE,
				'description'=> TRUE,
				'location'=> TRUE,
				'submissionDate'=> FALSE,
				'completionDate'=> FALSE,
				'feedback'=> FALSE,
				'isRepaired'=> TRUE
			);
		}else{
			$fields = array(
				'report_id'=> FALSE,
				'tech'=> FALSE,
				'reporter'=> FALSE,
				'description'=> TRUE,
				'location'=> TRUE,
				'submissionDate'=> FALSE,
				'completionDate'=> FALSE,
				'feedback'=> TRUE,
				'isRepaired'=> FALSE
			);
		}
			
		return $fields;
	}
}
?>