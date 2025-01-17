<?php

class Paging {
    
    private $total;
    private $delta;
    private $path;
    
    public function __construct(array $params) {        
        $this->delta = $params['delta'];
        $this->path = $params['path'];
    }
       
    public function render_array($total_page, $current_page, $method, $accesskey) {
        $page_numbers = '';
        
        if ($total_page >= $this->delta) {
            // if current page eq. 1, don't show prev and first button
            // print number 1 ... total page instead
            if ($current_page == 1) {
                for ($i=$current_page; $i < ($current_page + $this->delta); $i++) {
                    $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $i . '</a></li>';
                }
                
                // last page number button
                $page_numbers .= '<li><a href="#">...</a><li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $total_page . '</a></li>';
                
                // next and last button
                $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '&method=' . $method . '&accesskey=' . $accesskey . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                    <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '&method=' . $method . '&accesskey=' . $accesskey . '\');">Terakhir</a></li>';
            } else if ($current_page > 1) {
                if ($current_page > $this->delta) {
                    if ($current_page == $total_page) {
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1&method=' . $method . '&accesskey=' . $accesskey . '\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '&method=' . $method . '&accesskey=' . $accesskey . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                        
                            $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1&method=' . $method . '&accesskey=' . $accesskey . '\');">1</a></li><li><a href="#">...</a></li>';
                            
                        for ($i=($current_page - ($this->delta - 1)); $i <= $current_page; $i++) {
                            $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $i . '</a></li>';
                        }
                    } else if ($total_page - $current_page < $this->delta) {
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1&method=' . $method . '&accesskey=' . $accesskey . '\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '&method=' . $method . '&accesskey=' . $accesskey . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                        
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1&method=' . $method . '&accesskey=' . $accesskey . '\');">1</a></li><li><a href="#">...</a></li>';
                        
                        for ($i=($current_page - 1); $i < ($total_page + 1); $i++) {
                            $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $i . '</a></li>';
                        }
                    } else {
                        // if current page greater than 1 and greater than delta num, show prev and first button
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1&method=' . $method . '&accesskey=' . $accesskey . '\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '&method=' . $method . '&accesskey=' . $accesskey . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                        
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1&method=' . $method . '&accesskey=' . $accesskey . '\');">1</a></li><li><a href="#">...</a></li>';
                        
                        for ($i=($current_page - 1); $i < ($current_page + $this->delta); $i++) {
                            $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $i . '</a></li>';
                        }
                        
                        // last page number button
                        $page_numbers .= '<li><a href="#">...</a><li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $total_page . '</a></li>';
                        
                        $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '&method=' . $method . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                        <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '&method=' . $method . '&accesskey=' . $accesskey . '\');">Terakhir</a></li>';
                    }
                } else {
                    for ($i=1; $i < ($current_page + $this->delta); $i++) {
                        $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $i . '</a></li>';
                    }
                    
                    // last page number button
                    $page_numbers .= '<li><a href="#">...</a><li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $total_page . '</a></li>';
                    
                    $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '&method=' . $method . '&accesskey=' . $accesskey . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                    <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '&method=' . $method . '&accesskey=' . $accesskey . '\');">Terakhir</a></li>';
                }
            }  
        } else if ($total_page > 1) {
            if ($current_page == 1) {
                for ($i=$current_page; $i <= $total_page; $i++) {
                    $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $i . '</a></li>';
                }
                
                // next and last button
                $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '&method=' . $method . '&accesskey=' . $accesskey . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                    <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '&method=' . $method . '&accesskey=' . $accesskey . '\');">Terakhir</a></li>';
            } else {
                $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1&method=' . $method . '&accesskey=' . $accesskey . '\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '&method=' . $method . '&accesskey=' . $accesskey . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                
                for ($i=($current_page - 1); $i < ($total_page + 1); $i++) {
                    $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '&method=' . $method . '&accesskey=' . $accesskey . '\');">' . $i . '</a></li>';
                }                                
            }
        }
        
        return $page_numbers;           
    }
    
    public function render($total_page, $current_page) {
        $page_numbers = '';
        
        if ($total_page >= $this->delta) {
            // if current page eq. 1, don't show prev and first button
            // print number 1 ... total page instead
            if ($current_page == 1) {
                for ($i=$current_page; $i < ($current_page + $this->delta); $i++) {
                    $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '\');">' . $i . '</a></li>';
                }
                
                // last page number button
                $page_numbers .= '<li><a href="#">...</a><li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '\');">' . $total_page . '</a></li>';
                
                // next and last button
                $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                    <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '\');">Terakhir</a></li>';
            } else if ($current_page > 1) {
                if ($current_page > $this->delta) {
                    if ($current_page == $total_page) {
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                        
                            $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1\');">1</a></li><li><a href="#">...</a></li>';
                            
                        for ($i=($current_page - ($this->delta - 1)); $i <= $current_page; $i++) {
                            $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '\');">' . $i . '</a></li>';
                        }
                    } else if ($total_page - $current_page < $this->delta) {
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                        
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1\');">1</a></li><li><a href="#">...</a></li>';
                        
                        for ($i=($current_page - 1); $i < ($total_page + 1); $i++) {
                            $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '\');">' . $i . '</a></li>';
                        }
                    } else {
                        // if current page greater than 1 and greater than delta num, show prev and first button
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                        
                        $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1\');">1</a></li><li><a href="#">...</a></li>';
                        
                        for ($i=($current_page - 1); $i < ($current_page + $this->delta); $i++) {
                            $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '\');">' . $i . '</a></li>';
                        }
                        
                        // last page number button
                        $page_numbers .= '<li><a href="#">...</a><li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '\');">' . $total_page . '</a></li>';
                        
                        $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                        <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '\');">Terakhir</a></li>';
                    }
                } else {
                    for ($i=1; $i < ($current_page + $this->delta); $i++) {
                        $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '\');">' . $i . '</a></li>';
                    }
                    
                    // last page number button
                    $page_numbers .= '<li><a href="#">...</a><li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '\');">' . $total_page . '</a></li>';
                    
                    $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                    <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '\');">Terakhir</a></li>';
                }
            }  
        } else if ($total_page > 1) {
            if ($current_page == 1) {
                for ($i=$current_page; $i <= $total_page; $i++) {
                    $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '\');">' . $i . '</a></li>';
                }
                
                // next and last button
                $page_numbers .= '<li><a class="next" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page + 1) . '\');" href="#"><i class="fa fa-angle-right"></i></a></li>
                    <li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $total_page . '\');">Terakhir</a></li>';
            } else {
                $page_numbers .= '<li><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=1\');">Pertama</a></li><li><a class="prev" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . ($current_page - 1) . '\');" href="#"><i class="fa fa-angle-left"></i></a></li>';
                
                for ($i=($current_page - 1); $i < ($total_page + 1); $i++) {
                    $page_numbers .= '<li class="' . ($i == $current_page ? 'is-active-pagination' : '') . '"><a href="#" onclick="loadContent(\'#content_container\', \'' . $this->path . '?page=' . $i . '\');">' . $i . '</a></li>';
                }                                
            }
        }
        
        return $page_numbers;           
    }
}