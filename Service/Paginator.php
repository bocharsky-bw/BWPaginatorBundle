<?php

namespace BW\PaginatorBundle\Service;

class Paginator
{
    /**
     * Twig_Environment instance
     * @param \Twig_Environment $twig
     */
    protected $twig = NULL;
    
    /**
     * Данные, передаваемые в вид
     * @var array $data
     */
    protected $data = array();
    
    
    /**
     * Запрашиваемая страница
     * @var int
     */
    private $currentPage;
    
    /**
     * Количество ссылок на страницы
     * @var int
     */
    private $linkCount;
    
    /**
     * Реальное количество всех записей в таблице
     * @var int
     */
    private $allRowCount;
    
    /**
     * Ожидаемое количество записей
     * @var int
     */
    private $expectedRowCount;
    
    /**
     * Количество рядков, которые показываются на одной странице (параметр для LIMIT в SQL запросе)
     * @var int
     */
    private $rowCount;
    
    /**
     * Количество рядков для смещения (параметр для LIMIT в SQL запросе)
     * @var int
     */
    private $offset;
    
    /**
     * Номер первой ссылки
     * @var int
     */
    private $firstPage;
    
    /**
     * Номер последней ссылки
     * @var int
     */
    private $lastPage;
    
    /**
     * Номер начальной страницы в цикле
     * @var int
     */
    private $startPage;
    
    /**
     * Номер конечной страницы в цикле
     * @var int
     */
    private $endPage;
    
    
    /**
     * Конструктор для пагинатора
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig, $linkCount = 9, $rowCount = 5) {
        $this->twig = $twig;
        $this->setLinkCount($linkCount);
        $this->setRowCount($rowCount);
    }
    
    
    public function _initialize($allRowCount, $currentPage = 1, $linkCount = NULL, $rowCount = NULL) {
        $this->setAllRowCount($allRowCount);
        $this->setFirstPage();
        $this->setCurrentPage($currentPage);
        if ( !is_null($linkCount) ) {
            $this->setLinkCount($linkCount);
        }
        if ( !is_null($rowCount) ) {
            $this->setRowCount($rowCount);
        }
        $this->setLastPage();
        $this->setOffset();
        $this->setCycleRange();
        
        return $this;
    }
    
    private function setAllRowCount($allRowCount) {
        $this->allRowCount = (int) $allRowCount;
    }
    public function getAllRowCount() {
        
        return $this->allRowCount;
    }
    
    private function setCurrentPage($currentPage = 1) {
        $currentPage = (int) $currentPage;
        $this->currentPage = ( $currentPage > 0 ? $currentPage : $this->firstPage );
    }
    public function getCurrentPage() {
        
        return $this->currentPage;
    }
    
    private function setLinkCount($linkCount = 9) {
        $linkCount = (int) $linkCount;
        $this->linkCount = ( $linkCount > 0 ? $linkCount : 9 );
    }
    public function getLinkCount() {
        
        return $this->linkCount;
    }

    private function setRowCount($rowCount = 5) {
        $rowCount = (int) $rowCount;
        $this->rowCount = ( $rowCount > 0 ? $rowCount : 5 );
    }
    public function getRowCount() {
        
        return $this->rowCount;
    }
    
    private function setFirstPage() {
        $this->firstPage = 1;
    }
    public function getFirstPage() {
        
        return $this->firstPage;
    }
    
    private function setLastPage() {
        $this->lastPage = (int) ceil($this->allRowCount / $this->rowCount);
    }
    public function getLastPage() {
        
        return $this->lastPage;
    }
    
    private function setOffset() {
        $this->offset = (int) ( ($this->currentPage - 1) * $this->rowCount );
    }
    public function getOffset() {
        
        return $this->offset;
    }
    
    private function setCycleRange() {
        $this->expectedRowCount = (int) ( $this->rowCount * $this->linkCount );
        
        if ( $this->allRowCount > $this->expectedRowCount ) {
            // Полная навигация
            $this->startPage = (int) floor( $this->currentPage - ($this->linkCount / 2) );
            $this->endPage = (int) floor( $this->currentPage + ($this->linkCount / 2) );
        
            if ($this->startPage < $this->firstPage) {
                $this->startPage = $this->firstPage;
                $this->endPage = $this->linkCount + 1;
            } elseif ($this->endPage > $this->lastPage) {
                $this->startPage = $this->lastPage - $this->linkCount;
                $this->endPage = $this->lastPage;
            }
        } else {
            // Сокращенная навигация
            $this->startPage = 1;
            $this->endPage = (int) ceil( $this->allRowCount / $this->rowCount );
        }
    }
    public function getStartPage() {
        
        return $this->startPage;
    }
    public function getEndPage() {
        
        return $this->endPage;
    }

    
    public function getPagination() {
        $this->data['this'] = $this;
        
        //return $this->twig->render('BWPaginatorBundle:Paginator:google-classic-pagination.html.twig', $this->data);
        return $this->twig->render('BWPaginatorBundle:Paginator:first-last-page-pagination.html.twig', $this->data);
    }
    
    public function __toString() {
        return $this->getPagination();
    }
}
