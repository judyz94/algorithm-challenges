<?php
/**
 * LRUCache - Implementación de una caché de tipo Least Recently Used
 * Complejidad:
 *  - get(): O(1)
 *  - put(): O(1)
 * Espacio: O(n)
 */

class Node {
    public $key;
    public $value;
    public $prev;
    public $next;

    public function __construct($key, $value) {
        $this->key = $key;
        $this->value = $value;
        $this->prev = null;
        $this->next = null;
    }
}

class LRUCache {
    private $capacity;
    private $map = [];
    private $head;
    private $tail;

    public function __construct($capacity) {
        $this->capacity = $capacity;

        // Nodos centinela (cabeza y cola)
        $this->head = new Node(null, null);
        $this->tail = new Node(null, null);
        $this->head->next = $this->tail;
        $this->tail->prev = $this->head;
    }

    /** Obtiene un valor del caché */
    public function get($key) {
        if (!isset($this->map[$key])) return null;

        $node = $this->map[$key];
        $this->moveToHead($node);
        return $node->value;
    }

    /** Almacena o actualiza un valor en el caché */
    public function put($key, $value) {
        if (isset($this->map[$key])) {
            $node = $this->map[$key];
            $node->value = $value;
            $this->moveToHead($node);
        } else {
            $newNode = new Node($key, $value);
            $this->map[$key] = $newNode;
            $this->addNode($newNode);

            if (count($this->map) > $this->capacity) {
                $tail = $this->popTail();
                unset($this->map[$tail->key]);
            }
        }
    }

    /** Mueve un nodo al inicio (más recientemente usado) */
    private function moveToHead($node) {
        $this->removeNode($node);
        $this->addNode($node);
    }

    /** Inserta un nodo justo después de la cabeza */
    private function addNode($node) {
        $node->prev = $this->head;
        $node->next = $this->head->next;
        $this->head->next->prev = $node;
        $this->head->next = $node;
    }

    /** Elimina un nodo de la lista */
    private function removeNode($node) {
        $prev = $node->prev;
        $next = $node->next;
        $prev->next = $next;
        $next->prev = $prev;
    }

    /** Elimina el nodo menos recientemente usado (al final) */
    private function popTail() {
        $res = $this->tail->prev;
        $this->removeNode($res);
        return $res;
    }
}

/** Casos de prueba */
$cache = new LRUCache(3);
$cache->put("a", 1);
$cache->put("b", 2);
$cache->put("c", 3);
echo $cache->get("a") . PHP_EOL; // 1 (mueve "a" al frente)
$cache->put("d", 4); // elimina "b"
var_dump($cache->get("b")); // null
var_dump($cache->get("c")); // 3

