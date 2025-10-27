/**
 * LRUCache - Implementación de una caché de tipo Least Recently Used
 * Complejidad:
 *  - get(): O(1)
 *  - put(): O(1)
 * Espacio: O(n)
 */
class Node {
    constructor(key, value) {
        this.key = key;
        this.value = value;
        this.prev = null;
        this.next = null;
    }
}

class LRUCache {
    constructor(capacity) {
        this.capacity = capacity;
        this.map = new Map();

        // Nodos centinela
        this.head = new Node(null, null);
        this.tail = new Node(null, null);
        this.head.next = this.tail;
        this.tail.prev = this.head;
    }

    /** Obtiene un valor del caché */
    get(key) {
        if (!this.map.has(key)) return null;
        const node = this.map.get(key);
        this._moveToHead(node);
        return node.value;
    }

    /** Almacena o actualiza un valor en el caché */
    put(key, value) {
        if (this.map.has(key)) {
            const node = this.map.get(key);
            node.value = value;
            this._moveToHead(node);
        } else {
            const newNode = new Node(key, value);
            this.map.set(key, newNode);
            this._addNode(newNode);

            if (this.map.size > this.capacity) {
                const tail = this._popTail();
                this.map.delete(tail.key);
            }
        }
    }

    /** Inserta un nodo después de la cabeza */
    _addNode(node) {
        node.prev = this.head;
        node.next = this.head.next;
        this.head.next.prev = node;
        this.head.next = node;
    }

    /** Elimina un nodo */
    _removeNode(node) {
        const prev = node.prev;
        const next = node.next;
        prev.next = next;
        next.prev = prev;
    }

    /** Mueve un nodo al inicio (más recientemente usado) */
    _moveToHead(node) {
        this._removeNode(node);
        this._addNode(node);
    }

    /** Elimina el menos usado (final de la lista) */
    _popTail() {
        const res = this.tail.prev;
        this._removeNode(res);
        return res;
    }
}

/** Casos de prueba */
const cache = new LRUCache(3);

cache.put("a", 1);
cache.put("b", 2);
cache.put("c", 3);
console.log(cache.get("a")); // 1 (mueve "a" al frente)
cache.put("d", 4); // elimina "b"
console.log(cache.get("b")); // null
console.log(cache.get("c")); // 3
