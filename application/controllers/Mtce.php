<?php

class Mtce extends Application {

  private $items_per_page = 10;

  public function index()
  {
    $role = $this->session->userdata('userrole');
    $this->data['pagetitle'] = 'TODO List Maintenance ('. $role . ')';
    $this->page(1);
  }

  // Show a single page of todo items
  private function show_page($tasks)
  {
      $this->data['pagetitle'] = 'TODO List Maintenance';
      // build the task presentation output
      $result = ''; // start with an empty array
      foreach ($tasks as $task)
      {
          if (!empty($task->status))
              $task->status = $this->statuses->get($task->status)->name;
          $result .= $this->parser->parse('oneitem', (array) $task, true);
      }
      $this->data['display_tasks'] = $result;

      // and then pass them on
      $this->data['pagebody'] = 'itemlist';
      $this->render();
  }

  function page($num = 1) {
    $records = $this->tasks->all();
    $tasks = array();

    $index = 0;
    $count = 0;
    $start = ($num - 1) * $this->items_per_page;
    foreach($records as $task) {
      if($index++ >= $start) {
        $tasks[] = $task;
        $count++;
      }
      if($count >= $this->items_per_page) break;
    }
    $this->data['pagination'] = $this->pagenav($num);
    $this->show_page($tasks);
  }

  private function pagenav($num) {
    $lastpage = ceil($this->tasks->size() / $this->items_per_page);
    $parms = array(
      'first' => 1,
      'previous' => (max($num-1,1)),
      'next' => min($num+1,$lastpage),
      'last' => $lastpage
    );
    return $this->parser->parse('itemnav',$parms,true);
  }
}
