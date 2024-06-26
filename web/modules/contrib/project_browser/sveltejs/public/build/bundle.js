var app = (function () {
    'use strict';

    function noop() { }
    const identity = x => x;
    function assign(tar, src) {
        // @ts-ignore
        for (const k in src)
            tar[k] = src[k];
        return tar;
    }
    function is_promise(value) {
        return value && typeof value === 'object' && typeof value.then === 'function';
    }
    function run(fn) {
        return fn();
    }
    function blank_object() {
        return Object.create(null);
    }
    function run_all(fns) {
        fns.forEach(run);
    }
    function is_function(thing) {
        return typeof thing === 'function';
    }
    function safe_not_equal(a, b) {
        return a != a ? b == b : a !== b || ((a && typeof a === 'object') || typeof a === 'function');
    }
    let src_url_equal_anchor;
    function src_url_equal(element_src, url) {
        if (!src_url_equal_anchor) {
            src_url_equal_anchor = document.createElement('a');
        }
        src_url_equal_anchor.href = url;
        return element_src === src_url_equal_anchor.href;
    }
    function is_empty(obj) {
        return Object.keys(obj).length === 0;
    }
    function subscribe(store, ...callbacks) {
        if (store == null) {
            return noop;
        }
        const unsub = store.subscribe(...callbacks);
        return unsub.unsubscribe ? () => unsub.unsubscribe() : unsub;
    }
    function component_subscribe(component, store, callback) {
        component.$$.on_destroy.push(subscribe(store, callback));
    }
    function create_slot(definition, ctx, $$scope, fn) {
        if (definition) {
            const slot_ctx = get_slot_context(definition, ctx, $$scope, fn);
            return definition[0](slot_ctx);
        }
    }
    function get_slot_context(definition, ctx, $$scope, fn) {
        return definition[1] && fn
            ? assign($$scope.ctx.slice(), definition[1](fn(ctx)))
            : $$scope.ctx;
    }
    function get_slot_changes(definition, $$scope, dirty, fn) {
        if (definition[2] && fn) {
            const lets = definition[2](fn(dirty));
            if ($$scope.dirty === undefined) {
                return lets;
            }
            if (typeof lets === 'object') {
                const merged = [];
                const len = Math.max($$scope.dirty.length, lets.length);
                for (let i = 0; i < len; i += 1) {
                    merged[i] = $$scope.dirty[i] | lets[i];
                }
                return merged;
            }
            return $$scope.dirty | lets;
        }
        return $$scope.dirty;
    }
    function update_slot_base(slot, slot_definition, ctx, $$scope, slot_changes, get_slot_context_fn) {
        if (slot_changes) {
            const slot_context = get_slot_context(slot_definition, ctx, $$scope, get_slot_context_fn);
            slot.p(slot_context, slot_changes);
        }
    }
    function get_all_dirty_from_scope($$scope) {
        if ($$scope.ctx.length > 32) {
            const dirty = [];
            const length = $$scope.ctx.length / 32;
            for (let i = 0; i < length; i++) {
                dirty[i] = -1;
            }
            return dirty;
        }
        return -1;
    }
    function exclude_internal_props(props) {
        const result = {};
        for (const k in props)
            if (k[0] !== '$')
                result[k] = props[k];
        return result;
    }
    function compute_rest_props(props, keys) {
        const rest = {};
        keys = new Set(keys);
        for (const k in props)
            if (!keys.has(k) && k[0] !== '$')
                rest[k] = props[k];
        return rest;
    }
    function set_store_value(store, ret, value) {
        store.set(value);
        return ret;
    }

    const is_client = typeof window !== 'undefined';
    let now = is_client
        ? () => window.performance.now()
        : () => Date.now();
    let raf = is_client ? cb => requestAnimationFrame(cb) : noop;

    const tasks = new Set();
    function run_tasks(now) {
        tasks.forEach(task => {
            if (!task.c(now)) {
                tasks.delete(task);
                task.f();
            }
        });
        if (tasks.size !== 0)
            raf(run_tasks);
    }
    /**
     * Creates a new task that runs on each raf frame
     * until it returns a falsy value or is aborted
     */
    function loop(callback) {
        let task;
        if (tasks.size === 0)
            raf(run_tasks);
        return {
            promise: new Promise(fulfill => {
                tasks.add(task = { c: callback, f: fulfill });
            }),
            abort() {
                tasks.delete(task);
            }
        };
    }
    function append(target, node) {
        target.appendChild(node);
    }
    function get_root_for_style(node) {
        if (!node)
            return document;
        const root = node.getRootNode ? node.getRootNode() : node.ownerDocument;
        if (root && root.host) {
            return root;
        }
        return node.ownerDocument;
    }
    function append_empty_stylesheet(node) {
        const style_element = element('style');
        append_stylesheet(get_root_for_style(node), style_element);
        return style_element.sheet;
    }
    function append_stylesheet(node, style) {
        append(node.head || node, style);
    }
    function insert(target, node, anchor) {
        target.insertBefore(node, anchor || null);
    }
    function detach(node) {
        node.parentNode.removeChild(node);
    }
    function destroy_each(iterations, detaching) {
        for (let i = 0; i < iterations.length; i += 1) {
            if (iterations[i])
                iterations[i].d(detaching);
        }
    }
    function element(name) {
        return document.createElement(name);
    }
    function text(data) {
        return document.createTextNode(data);
    }
    function space() {
        return text(' ');
    }
    function empty() {
        return text('');
    }
    function listen(node, event, handler, options) {
        node.addEventListener(event, handler, options);
        return () => node.removeEventListener(event, handler, options);
    }
    function prevent_default(fn) {
        return function (event) {
            event.preventDefault();
            // @ts-ignore
            return fn.call(this, event);
        };
    }
    function attr(node, attribute, value) {
        if (value == null)
            node.removeAttribute(attribute);
        else if (node.getAttribute(attribute) !== value)
            node.setAttribute(attribute, value);
    }
    function set_attributes(node, attributes) {
        // @ts-ignore
        const descriptors = Object.getOwnPropertyDescriptors(node.__proto__);
        for (const key in attributes) {
            if (attributes[key] == null) {
                node.removeAttribute(key);
            }
            else if (key === 'style') {
                node.style.cssText = attributes[key];
            }
            else if (key === '__value') {
                node.value = node[key] = attributes[key];
            }
            else if (descriptors[key] && descriptors[key].set) {
                node[key] = attributes[key];
            }
            else {
                attr(node, key, attributes[key]);
            }
        }
    }
    function get_binding_group_value(group, __value, checked) {
        const value = new Set();
        for (let i = 0; i < group.length; i += 1) {
            if (group[i].checked)
                value.add(group[i].__value);
        }
        if (!checked) {
            value.delete(__value);
        }
        return Array.from(value);
    }
    function children(element) {
        return Array.from(element.childNodes);
    }
    function set_data(text, data) {
        data = '' + data;
        if (text.wholeText !== data)
            text.data = data;
    }
    function set_input_value(input, value) {
        input.value = value == null ? '' : value;
    }
    function set_style(node, key, value, important) {
        if (value === null) {
            node.style.removeProperty(key);
        }
        else {
            node.style.setProperty(key, value, important ? 'important' : '');
        }
    }
    function select_option(select, value) {
        for (let i = 0; i < select.options.length; i += 1) {
            const option = select.options[i];
            if (option.__value === value) {
                option.selected = true;
                return;
            }
        }
        select.selectedIndex = -1; // no option should be selected
    }
    function select_value(select) {
        const selected_option = select.querySelector(':checked') || select.options[0];
        return selected_option && selected_option.__value;
    }
    function toggle_class(element, name, toggle) {
        element.classList[toggle ? 'add' : 'remove'](name);
    }
    function custom_event(type, detail, { bubbles = false, cancelable = false } = {}) {
        const e = document.createEvent('CustomEvent');
        e.initCustomEvent(type, bubbles, cancelable, detail);
        return e;
    }

    // we need to store the information for multiple documents because a Svelte application could also contain iframes
    // https://github.com/sveltejs/svelte/issues/3624
    const managed_styles = new Map();
    let active = 0;
    // https://github.com/darkskyapp/string-hash/blob/master/index.js
    function hash(str) {
        let hash = 5381;
        let i = str.length;
        while (i--)
            hash = ((hash << 5) - hash) ^ str.charCodeAt(i);
        return hash >>> 0;
    }
    function create_style_information(doc, node) {
        const info = { stylesheet: append_empty_stylesheet(node), rules: {} };
        managed_styles.set(doc, info);
        return info;
    }
    function create_rule(node, a, b, duration, delay, ease, fn, uid = 0) {
        const step = 16.666 / duration;
        let keyframes = '{\n';
        for (let p = 0; p <= 1; p += step) {
            const t = a + (b - a) * ease(p);
            keyframes += p * 100 + `%{${fn(t, 1 - t)}}\n`;
        }
        const rule = keyframes + `100% {${fn(b, 1 - b)}}\n}`;
        const name = `__svelte_${hash(rule)}_${uid}`;
        const doc = get_root_for_style(node);
        const { stylesheet, rules } = managed_styles.get(doc) || create_style_information(doc, node);
        if (!rules[name]) {
            rules[name] = true;
            stylesheet.insertRule(`@keyframes ${name} ${rule}`, stylesheet.cssRules.length);
        }
        const animation = node.style.animation || '';
        node.style.animation = `${animation ? `${animation}, ` : ''}${name} ${duration}ms linear ${delay}ms 1 both`;
        active += 1;
        return name;
    }
    function delete_rule(node, name) {
        const previous = (node.style.animation || '').split(', ');
        const next = previous.filter(name
            ? anim => anim.indexOf(name) < 0 // remove specific animation
            : anim => anim.indexOf('__svelte') === -1 // remove all Svelte animations
        );
        const deleted = previous.length - next.length;
        if (deleted) {
            node.style.animation = next.join(', ');
            active -= deleted;
            if (!active)
                clear_rules();
        }
    }
    function clear_rules() {
        raf(() => {
            if (active)
                return;
            managed_styles.forEach(info => {
                const { stylesheet } = info;
                let i = stylesheet.cssRules.length;
                while (i--)
                    stylesheet.deleteRule(i);
                info.rules = {};
            });
            managed_styles.clear();
        });
    }

    let current_component;
    function set_current_component(component) {
        current_component = component;
    }
    function get_current_component() {
        if (!current_component)
            throw new Error('Function called outside component initialization');
        return current_component;
    }
    function onMount(fn) {
        get_current_component().$$.on_mount.push(fn);
    }
    function createEventDispatcher() {
        const component = get_current_component();
        return (type, detail, { cancelable = false } = {}) => {
            const callbacks = component.$$.callbacks[type];
            if (callbacks) {
                // TODO are there situations where events could be dispatched
                // in a server (non-DOM) environment?
                const event = custom_event(type, detail, { cancelable });
                callbacks.slice().forEach(fn => {
                    fn.call(component, event);
                });
                return !event.defaultPrevented;
            }
            return true;
        };
    }
    function setContext(key, context) {
        get_current_component().$$.context.set(key, context);
        return context;
    }
    function getContext(key) {
        return get_current_component().$$.context.get(key);
    }
    // TODO figure out if we still want to support
    // shorthand events, or if we want to implement
    // a real bubbling mechanism
    function bubble(component, event) {
        const callbacks = component.$$.callbacks[event.type];
        if (callbacks) {
            // @ts-ignore
            callbacks.slice().forEach(fn => fn.call(this, event));
        }
    }

    const dirty_components = [];
    const binding_callbacks = [];
    const render_callbacks = [];
    const flush_callbacks = [];
    const resolved_promise = Promise.resolve();
    let update_scheduled = false;
    function schedule_update() {
        if (!update_scheduled) {
            update_scheduled = true;
            resolved_promise.then(flush);
        }
    }
    function add_render_callback(fn) {
        render_callbacks.push(fn);
    }
    function add_flush_callback(fn) {
        flush_callbacks.push(fn);
    }
    // flush() calls callbacks in this order:
    // 1. All beforeUpdate callbacks, in order: parents before children
    // 2. All bind:this callbacks, in reverse order: children before parents.
    // 3. All afterUpdate callbacks, in order: parents before children. EXCEPT
    //    for afterUpdates called during the initial onMount, which are called in
    //    reverse order: children before parents.
    // Since callbacks might update component values, which could trigger another
    // call to flush(), the following steps guard against this:
    // 1. During beforeUpdate, any updated components will be added to the
    //    dirty_components array and will cause a reentrant call to flush(). Because
    //    the flush index is kept outside the function, the reentrant call will pick
    //    up where the earlier call left off and go through all dirty components. The
    //    current_component value is saved and restored so that the reentrant call will
    //    not interfere with the "parent" flush() call.
    // 2. bind:this callbacks cannot trigger new flush() calls.
    // 3. During afterUpdate, any updated components will NOT have their afterUpdate
    //    callback called a second time; the seen_callbacks set, outside the flush()
    //    function, guarantees this behavior.
    const seen_callbacks = new Set();
    let flushidx = 0; // Do *not* move this inside the flush() function
    function flush() {
        const saved_component = current_component;
        do {
            // first, call beforeUpdate functions
            // and update components
            while (flushidx < dirty_components.length) {
                const component = dirty_components[flushidx];
                flushidx++;
                set_current_component(component);
                update(component.$$);
            }
            set_current_component(null);
            dirty_components.length = 0;
            flushidx = 0;
            while (binding_callbacks.length)
                binding_callbacks.pop()();
            // then, once components are updated, call
            // afterUpdate functions. This may cause
            // subsequent updates...
            for (let i = 0; i < render_callbacks.length; i += 1) {
                const callback = render_callbacks[i];
                if (!seen_callbacks.has(callback)) {
                    // ...so guard against infinite loops
                    seen_callbacks.add(callback);
                    callback();
                }
            }
            render_callbacks.length = 0;
        } while (dirty_components.length);
        while (flush_callbacks.length) {
            flush_callbacks.pop()();
        }
        update_scheduled = false;
        seen_callbacks.clear();
        set_current_component(saved_component);
    }
    function update($$) {
        if ($$.fragment !== null) {
            $$.update();
            run_all($$.before_update);
            const dirty = $$.dirty;
            $$.dirty = [-1];
            $$.fragment && $$.fragment.p($$.ctx, dirty);
            $$.after_update.forEach(add_render_callback);
        }
    }

    let promise;
    function wait() {
        if (!promise) {
            promise = Promise.resolve();
            promise.then(() => {
                promise = null;
            });
        }
        return promise;
    }
    function dispatch(node, direction, kind) {
        node.dispatchEvent(custom_event(`${direction ? 'intro' : 'outro'}${kind}`));
    }
    const outroing = new Set();
    let outros;
    function group_outros() {
        outros = {
            r: 0,
            c: [],
            p: outros // parent group
        };
    }
    function check_outros() {
        if (!outros.r) {
            run_all(outros.c);
        }
        outros = outros.p;
    }
    function transition_in(block, local) {
        if (block && block.i) {
            outroing.delete(block);
            block.i(local);
        }
    }
    function transition_out(block, local, detach, callback) {
        if (block && block.o) {
            if (outroing.has(block))
                return;
            outroing.add(block);
            outros.c.push(() => {
                outroing.delete(block);
                if (callback) {
                    if (detach)
                        block.d(1);
                    callback();
                }
            });
            block.o(local);
        }
    }
    const null_transition = { duration: 0 };
    function create_bidirectional_transition(node, fn, params, intro) {
        let config = fn(node, params);
        let t = intro ? 0 : 1;
        let running_program = null;
        let pending_program = null;
        let animation_name = null;
        function clear_animation() {
            if (animation_name)
                delete_rule(node, animation_name);
        }
        function init(program, duration) {
            const d = (program.b - t);
            duration *= Math.abs(d);
            return {
                a: t,
                b: program.b,
                d,
                duration,
                start: program.start,
                end: program.start + duration,
                group: program.group
            };
        }
        function go(b) {
            const { delay = 0, duration = 300, easing = identity, tick = noop, css } = config || null_transition;
            const program = {
                start: now() + delay,
                b
            };
            if (!b) {
                // @ts-ignore todo: improve typings
                program.group = outros;
                outros.r += 1;
            }
            if (running_program || pending_program) {
                pending_program = program;
            }
            else {
                // if this is an intro, and there's a delay, we need to do
                // an initial tick and/or apply CSS animation immediately
                if (css) {
                    clear_animation();
                    animation_name = create_rule(node, t, b, duration, delay, easing, css);
                }
                if (b)
                    tick(0, 1);
                running_program = init(program, duration);
                add_render_callback(() => dispatch(node, b, 'start'));
                loop(now => {
                    if (pending_program && now > pending_program.start) {
                        running_program = init(pending_program, duration);
                        pending_program = null;
                        dispatch(node, running_program.b, 'start');
                        if (css) {
                            clear_animation();
                            animation_name = create_rule(node, t, running_program.b, running_program.duration, 0, easing, config.css);
                        }
                    }
                    if (running_program) {
                        if (now >= running_program.end) {
                            tick(t = running_program.b, 1 - t);
                            dispatch(node, running_program.b, 'end');
                            if (!pending_program) {
                                // we're done
                                if (running_program.b) {
                                    // intro — we can tidy up immediately
                                    clear_animation();
                                }
                                else {
                                    // outro — needs to be coordinated
                                    if (!--running_program.group.r)
                                        run_all(running_program.group.c);
                                }
                            }
                            running_program = null;
                        }
                        else if (now >= running_program.start) {
                            const p = now - running_program.start;
                            t = running_program.a + running_program.d * easing(p / running_program.duration);
                            tick(t, 1 - t);
                        }
                    }
                    return !!(running_program || pending_program);
                });
            }
        }
        return {
            run(b) {
                if (is_function(config)) {
                    wait().then(() => {
                        // @ts-ignore
                        config = config();
                        go(b);
                    });
                }
                else {
                    go(b);
                }
            },
            end() {
                clear_animation();
                running_program = pending_program = null;
            }
        };
    }

    function handle_promise(promise, info) {
        const token = info.token = {};
        function update(type, index, key, value) {
            if (info.token !== token)
                return;
            info.resolved = value;
            let child_ctx = info.ctx;
            if (key !== undefined) {
                child_ctx = child_ctx.slice();
                child_ctx[key] = value;
            }
            const block = type && (info.current = type)(child_ctx);
            let needs_flush = false;
            if (info.block) {
                if (info.blocks) {
                    info.blocks.forEach((block, i) => {
                        if (i !== index && block) {
                            group_outros();
                            transition_out(block, 1, 1, () => {
                                if (info.blocks[i] === block) {
                                    info.blocks[i] = null;
                                }
                            });
                            check_outros();
                        }
                    });
                }
                else {
                    info.block.d(1);
                }
                block.c();
                transition_in(block, 1);
                block.m(info.mount(), info.anchor);
                needs_flush = true;
            }
            info.block = block;
            if (info.blocks)
                info.blocks[index] = block;
            if (needs_flush) {
                flush();
            }
        }
        if (is_promise(promise)) {
            const current_component = get_current_component();
            promise.then(value => {
                set_current_component(current_component);
                update(info.then, 1, info.value, value);
                set_current_component(null);
            }, error => {
                set_current_component(current_component);
                update(info.catch, 2, info.error, error);
                set_current_component(null);
                if (!info.hasCatch) {
                    throw error;
                }
            });
            // if we previously had a then/catch block, destroy it
            if (info.current !== info.pending) {
                update(info.pending, 0);
                return true;
            }
        }
        else {
            if (info.current !== info.then) {
                update(info.then, 1, info.value, promise);
                return true;
            }
            info.resolved = promise;
        }
    }
    function update_await_block_branch(info, ctx, dirty) {
        const child_ctx = ctx.slice();
        const { resolved } = info;
        if (info.current === info.then) {
            child_ctx[info.value] = resolved;
        }
        if (info.current === info.catch) {
            child_ctx[info.error] = resolved;
        }
        info.block.p(child_ctx, dirty);
    }
    function outro_and_destroy_block(block, lookup) {
        transition_out(block, 1, 1, () => {
            lookup.delete(block.key);
        });
    }
    function update_keyed_each(old_blocks, dirty, get_key, dynamic, ctx, list, lookup, node, destroy, create_each_block, next, get_context) {
        let o = old_blocks.length;
        let n = list.length;
        let i = o;
        const old_indexes = {};
        while (i--)
            old_indexes[old_blocks[i].key] = i;
        const new_blocks = [];
        const new_lookup = new Map();
        const deltas = new Map();
        i = n;
        while (i--) {
            const child_ctx = get_context(ctx, list, i);
            const key = get_key(child_ctx);
            let block = lookup.get(key);
            if (!block) {
                block = create_each_block(key, child_ctx);
                block.c();
            }
            else if (dynamic) {
                block.p(child_ctx, dirty);
            }
            new_lookup.set(key, new_blocks[i] = block);
            if (key in old_indexes)
                deltas.set(key, Math.abs(i - old_indexes[key]));
        }
        const will_move = new Set();
        const did_move = new Set();
        function insert(block) {
            transition_in(block, 1);
            block.m(node, next);
            lookup.set(block.key, block);
            next = block.first;
            n--;
        }
        while (o && n) {
            const new_block = new_blocks[n - 1];
            const old_block = old_blocks[o - 1];
            const new_key = new_block.key;
            const old_key = old_block.key;
            if (new_block === old_block) {
                // do nothing
                next = new_block.first;
                o--;
                n--;
            }
            else if (!new_lookup.has(old_key)) {
                // remove old block
                destroy(old_block, lookup);
                o--;
            }
            else if (!lookup.has(new_key) || will_move.has(new_key)) {
                insert(new_block);
            }
            else if (did_move.has(old_key)) {
                o--;
            }
            else if (deltas.get(new_key) > deltas.get(old_key)) {
                did_move.add(new_key);
                insert(new_block);
            }
            else {
                will_move.add(old_key);
                o--;
            }
        }
        while (o--) {
            const old_block = old_blocks[o];
            if (!new_lookup.has(old_block.key))
                destroy(old_block, lookup);
        }
        while (n)
            insert(new_blocks[n - 1]);
        return new_blocks;
    }

    function get_spread_update(levels, updates) {
        const update = {};
        const to_null_out = {};
        const accounted_for = { $$scope: 1 };
        let i = levels.length;
        while (i--) {
            const o = levels[i];
            const n = updates[i];
            if (n) {
                for (const key in o) {
                    if (!(key in n))
                        to_null_out[key] = 1;
                }
                for (const key in n) {
                    if (!accounted_for[key]) {
                        update[key] = n[key];
                        accounted_for[key] = 1;
                    }
                }
                levels[i] = n;
            }
            else {
                for (const key in o) {
                    accounted_for[key] = 1;
                }
            }
        }
        for (const key in to_null_out) {
            if (!(key in update))
                update[key] = undefined;
        }
        return update;
    }

    function bind(component, name, callback) {
        const index = component.$$.props[name];
        if (index !== undefined) {
            component.$$.bound[index] = callback;
            callback(component.$$.ctx[index]);
        }
    }
    function create_component(block) {
        block && block.c();
    }
    function mount_component(component, target, anchor, customElement) {
        const { fragment, on_mount, on_destroy, after_update } = component.$$;
        fragment && fragment.m(target, anchor);
        if (!customElement) {
            // onMount happens before the initial afterUpdate
            add_render_callback(() => {
                const new_on_destroy = on_mount.map(run).filter(is_function);
                if (on_destroy) {
                    on_destroy.push(...new_on_destroy);
                }
                else {
                    // Edge case - component was destroyed immediately,
                    // most likely as a result of a binding initialising
                    run_all(new_on_destroy);
                }
                component.$$.on_mount = [];
            });
        }
        after_update.forEach(add_render_callback);
    }
    function destroy_component(component, detaching) {
        const $$ = component.$$;
        if ($$.fragment !== null) {
            run_all($$.on_destroy);
            $$.fragment && $$.fragment.d(detaching);
            // TODO null out other refs, including component.$$ (but need to
            // preserve final state?)
            $$.on_destroy = $$.fragment = null;
            $$.ctx = [];
        }
    }
    function make_dirty(component, i) {
        if (component.$$.dirty[0] === -1) {
            dirty_components.push(component);
            schedule_update();
            component.$$.dirty.fill(0);
        }
        component.$$.dirty[(i / 31) | 0] |= (1 << (i % 31));
    }
    function init(component, options, instance, create_fragment, not_equal, props, append_styles, dirty = [-1]) {
        const parent_component = current_component;
        set_current_component(component);
        const $$ = component.$$ = {
            fragment: null,
            ctx: null,
            // state
            props,
            update: noop,
            not_equal,
            bound: blank_object(),
            // lifecycle
            on_mount: [],
            on_destroy: [],
            on_disconnect: [],
            before_update: [],
            after_update: [],
            context: new Map(options.context || (parent_component ? parent_component.$$.context : [])),
            // everything else
            callbacks: blank_object(),
            dirty,
            skip_bound: false,
            root: options.target || parent_component.$$.root
        };
        append_styles && append_styles($$.root);
        let ready = false;
        $$.ctx = instance
            ? instance(component, options.props || {}, (i, ret, ...rest) => {
                const value = rest.length ? rest[0] : ret;
                if ($$.ctx && not_equal($$.ctx[i], $$.ctx[i] = value)) {
                    if (!$$.skip_bound && $$.bound[i])
                        $$.bound[i](value);
                    if (ready)
                        make_dirty(component, i);
                }
                return ret;
            })
            : [];
        $$.update();
        ready = true;
        run_all($$.before_update);
        // `false` as a special case of no DOM component
        $$.fragment = create_fragment ? create_fragment($$.ctx) : false;
        if (options.target) {
            if (options.hydrate) {
                const nodes = children(options.target);
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.l(nodes);
                nodes.forEach(detach);
            }
            else {
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.c();
            }
            if (options.intro)
                transition_in(component.$$.fragment);
            mount_component(component, options.target, options.anchor, options.customElement);
            flush();
        }
        set_current_component(parent_component);
    }
    /**
     * Base class for Svelte components. Used when dev=false.
     */
    class SvelteComponent {
        $destroy() {
            destroy_component(this, 1);
            this.$destroy = noop;
        }
        $on(type, callback) {
            const callbacks = (this.$$.callbacks[type] || (this.$$.callbacks[type] = []));
            callbacks.push(callback);
            return () => {
                const index = callbacks.indexOf(callback);
                if (index !== -1)
                    callbacks.splice(index, 1);
            };
        }
        $set($$props) {
            if (this.$$set && !is_empty($$props)) {
                this.$$.skip_bound = true;
                this.$$set($$props);
                this.$$.skip_bound = false;
            }
        }
    }

    const subscriber_queue = [];
    /**
     * Creates a `Readable` store that allows reading by subscription.
     * @param value initial value
     * @param {StartStopNotifier}start start and stop notifications for subscriptions
     */
    function readable(value, start) {
        return {
            subscribe: writable(value, start).subscribe
        };
    }
    /**
     * Create a `Writable` store that allows both updating and reading by subscription.
     * @param {*=}value initial value
     * @param {StartStopNotifier=}start start and stop notifications for subscriptions
     */
    function writable(value, start = noop) {
        let stop;
        const subscribers = new Set();
        function set(new_value) {
            if (safe_not_equal(value, new_value)) {
                value = new_value;
                if (stop) { // store is ready
                    const run_queue = !subscriber_queue.length;
                    for (const subscriber of subscribers) {
                        subscriber[1]();
                        subscriber_queue.push(subscriber, value);
                    }
                    if (run_queue) {
                        for (let i = 0; i < subscriber_queue.length; i += 2) {
                            subscriber_queue[i][0](subscriber_queue[i + 1]);
                        }
                        subscriber_queue.length = 0;
                    }
                }
            }
        }
        function update(fn) {
            set(fn(value));
        }
        function subscribe(run, invalidate = noop) {
            const subscriber = [run, invalidate];
            subscribers.add(subscriber);
            if (subscribers.size === 1) {
                stop = start(set) || noop;
            }
            run(value);
            return () => {
                subscribers.delete(subscriber);
                if (subscribers.size === 0) {
                    stop();
                    stop = null;
                }
            };
        }
        return { set, update, subscribe };
    }
    function derived(stores, fn, initial_value) {
        const single = !Array.isArray(stores);
        const stores_array = single
            ? [stores]
            : stores;
        const auto = fn.length < 2;
        return readable(initial_value, (set) => {
            let inited = false;
            const values = [];
            let pending = 0;
            let cleanup = noop;
            const sync = () => {
                if (pending) {
                    return;
                }
                cleanup();
                const result = fn(single ? values[0] : values, set);
                if (auto) {
                    set(result);
                }
                else {
                    cleanup = is_function(result) ? result : noop;
                }
            };
            const unsubscribers = stores_array.map((store, i) => subscribe(store, (value) => {
                values[i] = value;
                pending &= ~(1 << i);
                if (inited) {
                    sync();
                }
            }, () => {
                pending |= (1 << i);
            }));
            inited = true;
            sync();
            return function stop() {
                run_all(unsubscribers);
                cleanup();
            };
        });
    }

    /*! *****************************************************************************
    Copyright (c) Microsoft Corporation.

    Permission to use, copy, modify, and/or distribute this software for any
    purpose with or without fee is hereby granted.

    THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
    REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
    INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
    LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
    OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
    PERFORMANCE OF THIS SOFTWARE.
    ***************************************************************************** */

    function __spreadArray(to, from, pack) {
        if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
            if (ar || !(i in from)) {
                if (!ar) ar = Array.prototype.slice.call(from, 0, i);
                ar[i] = from[i];
            }
        }
        return to.concat(ar || Array.prototype.slice.call(from));
    }

    function withPrevious(initValue, _a) {
        var _b = _a === void 0 ? {} : _a, _c = _b.numToTrack, numToTrack = _c === void 0 ? 1 : _c, _d = _b.initPrevious, initPrevious = _d === void 0 ? [] : _d, _e = _b.requireChange, requireChange = _e === void 0 ? true : _e, _f = _b.isEqual, isEqual = _f === void 0 ? function (a, b) { return a === b; } : _f;
        if (numToTrack <= 0) {
            throw new Error('Must track at least 1 previous');
        }
        // Generates an array of size numToTrack with the first element set to
        // initValue and all other elements set to ...initPrevious or null.
        var rest = initPrevious.slice(0, numToTrack);
        while (rest.length < numToTrack) {
            rest.push(null);
        }
        var values = writable(__spreadArray([initValue], rest, true));
        var updateCurrent = function (fn) {
            values.update(function ($values) {
                var newValue = fn($values[0]);
                // Prevent updates if values are equal as defined by an isEqual
                // comparison. By default, use a simple === comparison.
                if (requireChange && isEqual(newValue, $values[0])) {
                    return $values;
                }
                // Adds the new value to the front of the array and removes the oldest
                // value from the end.
                return __spreadArray([newValue], $values.slice(0, numToTrack), true);
            });
        };
        var current = {
            subscribe: derived(values, function ($values) { return $values[0]; }).subscribe,
            update: updateCurrent,
            set: function (newValue) {
                updateCurrent(function () { return newValue; });
            },
        };
        // Create an array of derived stores for every other element in the array.
        var others = __spreadArray([], Array(numToTrack), true).map(function (_, i) {
            return derived(values, function ($values) { return $values[i + 1]; });
        });
        return __spreadArray([current], others, true);
    }

    const MAINTENANCE_OPTIONS =
      drupalSettings.project_browser.maintenance_options;
    const SECURITY_OPTIONS = drupalSettings.project_browser.security_options;
    const DEVELOPMENT_OPTIONS =
      drupalSettings.project_browser.development_options;
    const SORT_OPTIONS = drupalSettings.project_browser.sort_options;
    const ACTIVELY_MAINTAINED_ID =
      drupalSettings.project_browser.special_ids.maintenance_status.id;
    const COVERED_ID =
      drupalSettings.project_browser.special_ids.security_coverage.id;
    const ALL_VALUES_ID =
      drupalSettings.project_browser.special_ids.all_values;
    const DEFAULT_SOURCE_ID =
      drupalSettings.project_browser.default_plugin_id;
    const CURRENT_SOURCES_KEYS =
      drupalSettings.project_browser.current_sources_keys;
    const ORIGIN_URL = drupalSettings.project_browser.origin_url;
    const MODULE_STATUS = drupalSettings.project_browser.modules;
    const FULL_MODULE_PATH = `${ORIGIN_URL}/${drupalSettings.project_browser.module_path}`;
    const ALLOW_UI_INSTALL = drupalSettings.project_browser.ui_install;
    const DARK_COLOR_SCHEME =
      matchMedia('(forced-colors: active)').matches &&
      matchMedia('(prefers-color-scheme: dark)').matches;
    const PM_VALIDATION_ERROR = drupalSettings.project_browser.pm_validation;
    const ACTIVE_PLUGINS = drupalSettings.project_browser.active_plugins;

    // eslint-disable-next-line import/no-extraneous-dependencies

    // Store for applied advanced filters.
    const storedFilters = JSON.parse(sessionStorage.getItem('advancedFilter')) || {
      developmentStatus: '',
      maintenanceStatus: '',
      securityCoverage: ''
    };
    const filters = writable(storedFilters);
    filters.subscribe((val) => sessionStorage.setItem('advancedFilter', JSON.stringify(val)));

    const rowsCount = writable(0);

    const filtersVocabularies = writable({
      developmentStatus: JSON.parse(localStorage.getItem('pb.developmentStatus')) || [],
      maintenanceStatus: JSON.parse(localStorage.getItem('pb.maintenanceStatus')) || [],
      securityCoverage: JSON.parse(localStorage.getItem('pb.securityCoverage')) || []
    });

    // Store for applied category filters.
    const storedModuleCategoryFilter = JSON.parse(sessionStorage.getItem('categoryFilter')) || [];
    const moduleCategoryFilter = writable(storedModuleCategoryFilter);
    moduleCategoryFilter.subscribe((val) => sessionStorage.setItem('categoryFilter', JSON.stringify(val)));

    // Store for module category vocabularies.
    const storedModuleCategoryVocabularies = JSON.parse(localStorage.getItem('moduleCategoryVocabularies')) || {};
    const moduleCategoryVocabularies = writable(storedModuleCategoryVocabularies);
    moduleCategoryVocabularies.subscribe((val) => localStorage.setItem('moduleCategoryVocabularies', JSON.stringify(val)));

    // Store used to check if the page has loaded once already.
    const storedIsFirstLoad = JSON.parse(sessionStorage.getItem('isFirstLoad')) === false ? JSON.parse(sessionStorage.getItem('isFirstLoad')) : true;
    const isFirstLoad = writable(storedIsFirstLoad);
    isFirstLoad.subscribe((val) => sessionStorage.setItem('isFirstLoad', JSON.stringify(val)));

    // Store the page the user is on.
    const storedPage = JSON.parse(sessionStorage.getItem('page')) || 0;
    const page = writable(storedPage);
    page.subscribe((val) => sessionStorage.setItem('page', JSON.stringify(val)));

    // Store the selected tab.
    const storedActiveTab = JSON.parse(sessionStorage.getItem('activeTab')) || DEFAULT_SOURCE_ID;
    const activeTab = writable(storedActiveTab);
    activeTab.subscribe((val) => sessionStorage.setItem('activeTab', JSON.stringify(val)));

    // Store the current sort selected.
    const storedSort = JSON.parse(sessionStorage.getItem('sort')) || SORT_OPTIONS[storedActiveTab][0].id;
    const sort = writable(storedSort);
    sort.subscribe((val) => sessionStorage.setItem('sort', JSON.stringify(val)));

    // Store tab-wise checked categories.
    const storedCategoryCheckedTrack = JSON.parse(sessionStorage.getItem('categoryCheckedTrack')) || {};
    const categoryCheckedTrack = writable(storedCategoryCheckedTrack);
    categoryCheckedTrack.subscribe((val) => sessionStorage.setItem('categoryCheckedTrack', JSON.stringify(val)));

    // Store the element that was last focused.
    const storedFocus = JSON.parse(sessionStorage.getItem('focusedElement')) || '';
    const focusedElement = writable(storedFocus);
    focusedElement.subscribe((val) => sessionStorage.setItem('focusedElement', JSON.stringify(val)));

    // Store the search string.
    const storedSearchString = JSON.parse(sessionStorage.getItem('searchString')) || '';
    const searchString = writable(storedSearchString);
    searchString.subscribe((val) => sessionStorage.setItem('searchString', JSON.stringify(val)));

    // Store for sort criteria.
    const storedSortCriteria = JSON.parse(sessionStorage.getItem('sortCriteria')) || SORT_OPTIONS[storedActiveTab];
    const sortCriteria = writable(storedSortCriteria);
    sortCriteria.subscribe((val) => sessionStorage.setItem('sortCriteria', JSON.stringify(val)));

    // Store the selected toggle view.
    const storedPreferredView = JSON.parse(sessionStorage.getItem('preferredView')) || 'Grid';
    const preferredView = writable(storedPreferredView);
    preferredView.subscribe((val) => sessionStorage.setItem('preferredView', JSON.stringify(val)));

    // Store the selected page size.
    const storedPageSize = JSON.parse(sessionStorage.getItem('pageSize')) || 12;
    const pageSize = writable(storedPageSize);
    pageSize.subscribe((val) => sessionStorage.setItem('pageSize', JSON.stringify(val)));

    // Store the Package Manager requirement.
    const isPackageManagerRequired = writable(false);

    // Store the value of media queries.
    const mediaQueryValues = writable(new Map());

    /* src/Loading.svelte generated by Svelte v3.48.0 */

    function create_fragment$p(ctx) {
    	let div1;
    	let div0;

    	return {
    		c() {
    			div1 = element("div");
    			div0 = element("div");
    			div0.textContent = " ";
    			attr(div0, "class", "ajax-progress__throbber");
    			toggle_class(div0, "ajax-progress__throbber--fullscreen", !/*inline*/ ctx[1]);
    			attr(div1, "class", "loading__ajax-progress");
    			toggle_class(div1, "absolute", /*positionAbsolute*/ ctx[0]);
    			toggle_class(div1, "ajax-progress--fullscreen", !/*inline*/ ctx[1]);
    		},
    		m(target, anchor) {
    			insert(target, div1, anchor);
    			append(div1, div0);
    		},
    		p(ctx, [dirty]) {
    			if (dirty & /*inline*/ 2) {
    				toggle_class(div0, "ajax-progress__throbber--fullscreen", !/*inline*/ ctx[1]);
    			}

    			if (dirty & /*positionAbsolute*/ 1) {
    				toggle_class(div1, "absolute", /*positionAbsolute*/ ctx[0]);
    			}

    			if (dirty & /*inline*/ 2) {
    				toggle_class(div1, "ajax-progress--fullscreen", !/*inline*/ ctx[1]);
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(div1);
    		}
    	};
    }

    function instance$p($$self, $$props, $$invalidate) {
    	let { positionAbsolute = false } = $$props;
    	let { inline = false } = $$props;

    	$$self.$$set = $$props => {
    		if ('positionAbsolute' in $$props) $$invalidate(0, positionAbsolute = $$props.positionAbsolute);
    		if ('inline' in $$props) $$invalidate(1, inline = $$props.inline);
    	};

    	return [positionAbsolute, inline];
    }

    class Loading extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$p, create_fragment$p, safe_not_equal, { positionAbsolute: 0, inline: 1 });
    	}
    }

    /* src/MediaQuery.svelte generated by Svelte v3.48.0 */
    const get_default_slot_changes$1 = dirty => ({ matches: dirty & /*matches*/ 1 });
    const get_default_slot_context$1 = ctx => ({ matches: /*matches*/ ctx[0] });

    function create_fragment$o(ctx) {
    	let current;
    	const default_slot_template = /*#slots*/ ctx[4].default;
    	const default_slot = create_slot(default_slot_template, ctx, /*$$scope*/ ctx[3], get_default_slot_context$1);

    	return {
    		c() {
    			if (default_slot) default_slot.c();
    		},
    		m(target, anchor) {
    			if (default_slot) {
    				default_slot.m(target, anchor);
    			}

    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if (default_slot) {
    				if (default_slot.p && (!current || dirty & /*$$scope, matches*/ 9)) {
    					update_slot_base(
    						default_slot,
    						default_slot_template,
    						ctx,
    						/*$$scope*/ ctx[3],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[3])
    						: get_slot_changes(default_slot_template, /*$$scope*/ ctx[3], dirty, get_default_slot_changes$1),
    						get_default_slot_context$1
    					);
    				}
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(default_slot, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(default_slot, local);
    			current = false;
    		},
    		d(detaching) {
    			if (default_slot) default_slot.d(detaching);
    		}
    	};
    }

    function instance$o($$self, $$props, $$invalidate) {
    	let $mediaQueryValues;
    	component_subscribe($$self, mediaQueryValues, $$value => $$invalidate(7, $mediaQueryValues = $$value));
    	let { $$slots: slots = {}, $$scope } = $$props;
    	let { query } = $$props;
    	let mql;
    	let mqlListener;
    	let wasMounted = false;
    	let matches = false;

    	// eslint-disable-next-line no-shadow
    	function addNewListener(query) {
    		mql = window.matchMedia(query);

    		mqlListener = v => {
    			$$invalidate(0, matches = v.matches);

    			// Update store values
    			const currentMqs = $mediaQueryValues;

    			currentMqs.set(query, matches);
    			set_store_value(mediaQueryValues, $mediaQueryValues = currentMqs, $mediaQueryValues);
    		};

    		mql.addEventListener('change', mqlListener);
    		$$invalidate(0, matches = mql.matches);

    		// Set store values on page load
    		const mqs = $mediaQueryValues;

    		mqs.set(query, matches);
    		set_store_value(mediaQueryValues, $mediaQueryValues = mqs, $mediaQueryValues);
    	}

    	function removeActiveListener() {
    		if (mql && mqlListener) {
    			mql.removeListener(mqlListener);
    		}
    	}

    	onMount(() => {
    		$$invalidate(2, wasMounted = true);

    		return () => {
    			removeActiveListener();
    		};
    	});

    	$$self.$$set = $$props => {
    		if ('query' in $$props) $$invalidate(1, query = $$props.query);
    		if ('$$scope' in $$props) $$invalidate(3, $$scope = $$props.$$scope);
    	};

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*wasMounted, query*/ 6) {
    			{
    				if (wasMounted) {
    					removeActiveListener();
    					addNewListener(query);
    				}
    			}
    		}
    	};

    	return [matches, query, wasMounted, $$scope, slots];
    }

    class MediaQuery extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$o, create_fragment$o, safe_not_equal, { query: 1 });
    	}
    }

    const normalizeOptions = (value) => {
      const newValue = {};
      const isArray = Array.isArray(value);
      if (isArray) {
        Object.values(value).forEach((item) => {
          newValue[item.id] = item.name;
        });
      } else {
        Object.entries(value).forEach(([id, name]) => {
          newValue[id] = name;
        });
      }

      return newValue;
    };

    const shallowCompare = (obj1, obj2) =>
      Object.keys(obj1).length === Object.keys(obj2).length &&
      Object.keys(obj1).every(
        (key) => obj2.hasOwnProperty(key) && obj1[key] === obj2[key],
      );

    const numberFormatter = new Intl.NumberFormat(navigator.language);

    /* src/Filter.svelte generated by Svelte v3.48.0 */

    function get_each_context$9(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[14] = list[i];
    	return child_ctx;
    }

    // (1:0) <script>   import { createEventDispatcher, getContext, onMount }
    function create_catch_block$2(ctx) {
    	return { c: noop, m: noop, p: noop, d: noop };
    }

    // (82:54)              {#each categoryList[$activeTab] as dt}
    function create_then_block$2(ctx) {
    	let each_1_anchor;
    	let each_value = /*categoryList*/ ctx[13][/*$activeTab*/ ctx[0]];
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$9(get_each_context$9(ctx, each_value, i));
    	}

    	return {
    		c() {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			each_1_anchor = empty();
    		},
    		m(target, anchor) {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(target, anchor);
    			}

    			insert(target, each_1_anchor, anchor);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*apiModuleCategory, $activeTab, $moduleCategoryFilter, onSelectCategory*/ 15) {
    				each_value = /*categoryList*/ ctx[13][/*$activeTab*/ ctx[0]];
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$9(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$9(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(each_1_anchor.parentNode, each_1_anchor);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}
    		},
    		d(detaching) {
    			destroy_each(each_blocks, detaching);
    			if (detaching) detach(each_1_anchor);
    		}
    	};
    }

    // (83:12) {#each categoryList[$activeTab] as dt}
    function create_each_block$9(ctx) {
    	let label;
    	let input;
    	let input_id_value;
    	let input_value_value;
    	let t_value = /*dt*/ ctx[14].name + "";
    	let t;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			label = element("label");
    			input = element("input");
    			t = text(t_value);
    			attr(input, "type", "checkbox");
    			attr(input, "id", input_id_value = /*dt*/ ctx[14].id);
    			attr(input, "class", "pb-filter__checkbox");
    			input.__value = input_value_value = /*dt*/ ctx[14].id;
    			input.value = input.__value;
    			/*$$binding_groups*/ ctx[6][0].push(input);
    			attr(label, "class", "pb-filter__checkbox-label");
    		},
    		m(target, anchor) {
    			insert(target, label, anchor);
    			append(label, input);
    			input.checked = ~/*$moduleCategoryFilter*/ ctx[1].indexOf(input.__value);
    			append(label, t);

    			if (!mounted) {
    				dispose = [
    					listen(input, "change", /*input_change_handler*/ ctx[5]),
    					listen(input, "change", /*onSelectCategory*/ ctx[2])
    				];

    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			if (dirty & /*$activeTab*/ 1 && input_id_value !== (input_id_value = /*dt*/ ctx[14].id)) {
    				attr(input, "id", input_id_value);
    			}

    			if (dirty & /*$activeTab*/ 1 && input_value_value !== (input_value_value = /*dt*/ ctx[14].id)) {
    				input.__value = input_value_value;
    				input.value = input.__value;
    			}

    			if (dirty & /*$moduleCategoryFilter*/ 2) {
    				input.checked = ~/*$moduleCategoryFilter*/ ctx[1].indexOf(input.__value);
    			}

    			if (dirty & /*$activeTab*/ 1 && t_value !== (t_value = /*dt*/ ctx[14].name + "")) set_data(t, t_value);
    		},
    		d(detaching) {
    			if (detaching) detach(label);
    			/*$$binding_groups*/ ctx[6][0].splice(/*$$binding_groups*/ ctx[6][0].indexOf(input), 1);
    			mounted = false;
    			run_all(dispose);
    		}
    	};
    }

    // (1:0) <script>   import { createEventDispatcher, getContext, onMount }
    function create_pending_block$2(ctx) {
    	return { c: noop, m: noop, p: noop, d: noop };
    }

    // (58:0) <MediaQuery query="(min-width: 800px)" let:matches>
    function create_default_slot$3(ctx) {
    	let form;
    	let section;
    	let details;
    	let summary;
    	let h20;
    	let summary_hidden_value;
    	let t1;
    	let fieldset;
    	let h21;
    	let t3;
    	let details_open_value;

    	let info = {
    		ctx,
    		current: null,
    		token: null,
    		hasCatch: false,
    		pending: create_pending_block$2,
    		then: create_then_block$2,
    		catch: create_catch_block$2,
    		value: 13
    	};

    	handle_promise(/*apiModuleCategory*/ ctx[3], info);

    	return {
    		c() {
    			form = element("form");
    			section = element("section");
    			details = element("details");
    			summary = element("summary");
    			h20 = element("h2");
    			h20.textContent = `${window.Drupal.t('Filter Categories')}`;
    			t1 = space();
    			fieldset = element("fieldset");
    			h21 = element("h2");
    			h21.textContent = `${window.Drupal.t('Filter Categories')}`;
    			t3 = space();
    			info.block.c();
    			attr(h20, "class", "pb-filter__heading pb-filter__heading--wide");
    			attr(summary, "class", "pb-filter__summary");
    			summary.hidden = summary_hidden_value = /*matches*/ ctx[12];
    			toggle_class(summary, "pb-filter__summary--open", /*matches*/ ctx[12]);
    			attr(h21, "class", "pb-filter__heading pb-filter__heading--narrow");
    			toggle_class(h21, "visually-hidden", !/*matches*/ ctx[12]);
    			attr(fieldset, "class", "pb-filter__fieldset");
    			attr(details, "class", "pb-filter__categories");
    			details.open = details_open_value = /*matches*/ ctx[12];
    			toggle_class(details, "pb-filter__categories--open", /*matches*/ ctx[12]);
    			attr(section, "aria-label", window.Drupal.t('Filter categories'));
    			attr(form, "class", "pb-filter");
    		},
    		m(target, anchor) {
    			insert(target, form, anchor);
    			append(form, section);
    			append(section, details);
    			append(details, summary);
    			append(summary, h20);
    			append(details, t1);
    			append(details, fieldset);
    			append(fieldset, h21);
    			append(fieldset, t3);
    			info.block.m(fieldset, info.anchor = null);
    			info.mount = () => fieldset;
    			info.anchor = null;
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;

    			if (dirty & /*matches*/ 4096 && summary_hidden_value !== (summary_hidden_value = /*matches*/ ctx[12])) {
    				summary.hidden = summary_hidden_value;
    			}

    			if (dirty & /*matches*/ 4096) {
    				toggle_class(summary, "pb-filter__summary--open", /*matches*/ ctx[12]);
    			}

    			if (dirty & /*matches*/ 4096) {
    				toggle_class(h21, "visually-hidden", !/*matches*/ ctx[12]);
    			}

    			update_await_block_branch(info, ctx, dirty);

    			if (dirty & /*matches*/ 4096 && details_open_value !== (details_open_value = /*matches*/ ctx[12])) {
    				details.open = details_open_value;
    			}

    			if (dirty & /*matches*/ 4096) {
    				toggle_class(details, "pb-filter__categories--open", /*matches*/ ctx[12]);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(form);
    			info.block.d();
    			info.token = null;
    			info = null;
    		}
    	};
    }

    function create_fragment$n(ctx) {
    	let mediaquery;
    	let current;

    	mediaquery = new MediaQuery({
    			props: {
    				query: "(min-width: 800px)",
    				$$slots: {
    					default: [
    						create_default_slot$3,
    						({ matches }) => ({ 12: matches }),
    						({ matches }) => matches ? 4096 : 0
    					]
    				},
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			create_component(mediaquery.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(mediaquery, target, anchor);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			const mediaquery_changes = {};

    			if (dirty & /*$$scope, matches, $activeTab, $moduleCategoryFilter*/ 135171) {
    				mediaquery_changes.$$scope = { dirty, ctx };
    			}

    			mediaquery.$set(mediaquery_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(mediaquery.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(mediaquery.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(mediaquery, detaching);
    		}
    	};
    }

    function instance$n($$self, $$props, $$invalidate) {
    	let $moduleCategoryVocabularies;
    	let $activeTab;
    	let $moduleCategoryFilter;
    	component_subscribe($$self, moduleCategoryVocabularies, $$value => $$invalidate(7, $moduleCategoryVocabularies = $$value));
    	component_subscribe($$self, activeTab, $$value => $$invalidate(0, $activeTab = $$value));
    	component_subscribe($$self, moduleCategoryFilter, $$value => $$invalidate(1, $moduleCategoryFilter = $$value));
    	const dispatch = createEventDispatcher();
    	const stateContext = getContext('state');

    	async function onSelectCategory(event) {
    		const state = stateContext.getState();

    		const detail = {
    			originalEvent: event,
    			category: $moduleCategoryFilter,
    			page: state.page,
    			pageIndex: state.pageIndex,
    			pageSize: state.pageSize,
    			rows: state.filteredRows
    		};

    		dispatch('selectCategory', detail);
    		stateContext.setPage(0, 0);
    		stateContext.setRows(detail.rows);
    	}

    	async function fetchAllCategories() {
    		const response = await fetch(`${ORIGIN_URL}/drupal-org-proxy/categories`);

    		if (response.ok) {
    			return response.json();
    		}

    		return [];
    	}

    	const apiModuleCategory = fetchAllCategories();

    	async function setModuleCategoryVocabulary() {
    		apiModuleCategory.then(value => {
    			const normalizedValue = normalizeOptions(value[$activeTab]);
    			const storedValue = $moduleCategoryVocabularies;

    			if (storedValue === null || !shallowCompare(normalizedValue, storedValue)) {
    				moduleCategoryVocabularies.set(normalizedValue);
    			}
    		});
    	}

    	onMount(async () => {
    		await setModuleCategoryVocabulary();
    	});

    	const $$binding_groups = [[]];

    	function input_change_handler() {
    		$moduleCategoryFilter = get_binding_group_value($$binding_groups[0], this.__value, this.checked);
    		moduleCategoryFilter.set($moduleCategoryFilter);
    	}

    	return [
    		$activeTab,
    		$moduleCategoryFilter,
    		onSelectCategory,
    		apiModuleCategory,
    		setModuleCategoryVocabulary,
    		input_change_handler,
    		$$binding_groups
    	];
    }

    class Filter extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$n, create_fragment$n, safe_not_equal, { setModuleCategoryVocabulary: 4 });
    	}

    	get setModuleCategoryVocabulary() {
    		return this.$$.ctx[4];
    	}
    }

    /* src/Search/FilterApplied.svelte generated by Svelte v3.48.0 */

    function create_if_block$d(ctx) {
    	let p;
    	let span;
    	let t0;
    	let t1;
    	let button;
    	let t2;
    	let img;
    	let img_src_value;
    	let mounted;
    	let dispose;
    	let if_block = /*label*/ ctx[1] && create_if_block_1$c(ctx);

    	return {
    		c() {
    			p = element("p");
    			span = element("span");
    			t0 = text(/*label*/ ctx[1]);
    			t1 = space();
    			button = element("button");
    			if (if_block) if_block.c();
    			t2 = space();
    			img = element("img");
    			attr(span, "class", "filter-applied__label");
    			if (!src_url_equal(img.src, img_src_value = "" + (FULL_MODULE_PATH + "/images/chip-close-icon.svg"))) attr(img, "src", img_src_value);
    			attr(img, "alt", "");
    			attr(button, "type", "button");
    			attr(button, "class", "filter-applied__button-close");
    			attr(p, "class", "filter-applied");
    		},
    		m(target, anchor) {
    			insert(target, p, anchor);
    			append(p, span);
    			append(span, t0);
    			append(p, t1);
    			append(p, button);
    			if (if_block) if_block.m(button, null);
    			append(button, t2);
    			append(button, img);

    			if (!mounted) {
    				dispose = listen(button, "click", function () {
    					if (is_function(/*clickHandler*/ ctx[2])) /*clickHandler*/ ctx[2].apply(this, arguments);
    				});

    				mounted = true;
    			}
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			if (dirty & /*label*/ 2) set_data(t0, /*label*/ ctx[1]);

    			if (/*label*/ ctx[1]) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block_1$c(ctx);
    					if_block.c();
    					if_block.m(button, t2);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(p);
    			if (if_block) if_block.d();
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (19:6) {#if label}
    function create_if_block_1$c(ctx) {
    	let span;
    	let t_value = window.Drupal.t('Remove @filter', { '@filter': /*label*/ ctx[1] }) + "";
    	let t;

    	return {
    		c() {
    			span = element("span");
    			t = text(t_value);
    			attr(span, "class", "visually-hidden");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    			append(span, t);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*label*/ 2 && t_value !== (t_value = window.Drupal.t('Remove @filter', { '@filter': /*label*/ ctx[1] }) + "")) set_data(t, t_value);
    		},
    		d(detaching) {
    			if (detaching) detach(span);
    		}
    	};
    }

    function create_fragment$m(ctx) {
    	let if_block_anchor;
    	let if_block = /*id*/ ctx[0] !== ALL_VALUES_ID && create_if_block$d(ctx);

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    		},
    		p(ctx, [dirty]) {
    			if (/*id*/ ctx[0] !== ALL_VALUES_ID) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block$d(ctx);
    					if_block.c();
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    function instance$m($$self, $$props, $$invalidate) {
    	let { id } = $$props;
    	let { label } = $$props;
    	let { clickHandler } = $$props;

    	$$self.$$set = $$props => {
    		if ('id' in $$props) $$invalidate(0, id = $$props.id);
    		if ('label' in $$props) $$invalidate(1, label = $$props.label);
    		if ('clickHandler' in $$props) $$invalidate(2, clickHandler = $$props.clickHandler);
    	};

    	return [id, label, clickHandler];
    }

    class FilterApplied extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$m, create_fragment$m, safe_not_equal, { id: 0, label: 1, clickHandler: 2 });
    	}
    }

    function cubicOut(t) {
        const f = t - 1.0;
        return f * f * f + 1.0;
    }

    function slide(node, { delay = 0, duration = 400, easing = cubicOut } = {}) {
        const style = getComputedStyle(node);
        const opacity = +style.opacity;
        const height = parseFloat(style.height);
        const padding_top = parseFloat(style.paddingTop);
        const padding_bottom = parseFloat(style.paddingBottom);
        const margin_top = parseFloat(style.marginTop);
        const margin_bottom = parseFloat(style.marginBottom);
        const border_top_width = parseFloat(style.borderTopWidth);
        const border_bottom_width = parseFloat(style.borderBottomWidth);
        return {
            delay,
            duration,
            easing,
            css: t => 'overflow: hidden;' +
                `opacity: ${Math.min(t * 20, 1) * opacity};` +
                `height: ${t * height}px;` +
                `padding-top: ${t * padding_top}px;` +
                `padding-bottom: ${t * padding_bottom}px;` +
                `margin-top: ${t * margin_top}px;` +
                `margin-bottom: ${t * margin_bottom}px;` +
                `border-top-width: ${t * border_top_width}px;` +
                `border-bottom-width: ${t * border_bottom_width}px;`
        };
    }

    /* src/Search/FilterGroup.svelte generated by Svelte v3.48.0 */

    function get_each_context$8(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[9] = list[i][0];
    	child_ctx[10] = list[i][1];
    	return child_ctx;
    }

    const get_label_slot_changes = dirty => ({
    	id: dirty & /*filterData*/ 2,
    	label: dirty & /*filterData*/ 2
    });

    const get_label_slot_context = ctx => ({
    	class: "filter-group__label-slot",
    	id: /*id*/ ctx[9],
    	label: /*label*/ ctx[10]
    });

    // (27:75)              
    function fallback_block(ctx) {
    	let label;
    	let t_value = /*label*/ ctx[10] + "";
    	let t;
    	let label_for_value;

    	return {
    		c() {
    			label = element("label");
    			t = text(t_value);
    			attr(label, "class", "filter-group__option-label");
    			attr(label, "for", label_for_value = /*filterType*/ ctx[3] + /*id*/ ctx[9]);
    		},
    		m(target, anchor) {
    			insert(target, label, anchor);
    			append(label, t);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*filterData*/ 2 && t_value !== (t_value = /*label*/ ctx[10] + "")) set_data(t, t_value);

    			if (dirty & /*filterType, filterData*/ 10 && label_for_value !== (label_for_value = /*filterType*/ ctx[3] + /*id*/ ctx[9])) {
    				attr(label, "for", label_for_value);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(label);
    		}
    	};
    }

    // (16:6) {#each Object.entries(filterData) as [id, label]}
    function create_each_block$8(ctx) {
    	let div;
    	let input;
    	let input_id_value;
    	let input_value_value;
    	let t0;
    	let t1;
    	let current;
    	let mounted;
    	let dispose;
    	const label_slot_template = /*#slots*/ ctx[6].label;
    	const label_slot = create_slot(label_slot_template, ctx, /*$$scope*/ ctx[5], get_label_slot_context);
    	const label_slot_or_fallback = label_slot || fallback_block(ctx);

    	return {
    		c() {
    			div = element("div");
    			input = element("input");
    			t0 = space();
    			if (label_slot_or_fallback) label_slot_or_fallback.c();
    			t1 = space();
    			attr(input, "type", "radio");
    			attr(input, "name", /*filterType*/ ctx[3]);
    			attr(input, "id", input_id_value = /*filterType*/ ctx[3] + /*id*/ ctx[9]);
    			attr(input, "class", "filter-group__radio form-radio form-boolean form-boolean--type-radio");
    			input.__value = input_value_value = /*id*/ ctx[9];
    			input.value = input.__value;
    			/*$$binding_groups*/ ctx[8][0].push(input);
    			attr(div, "class", "filter-group__filter-option");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			append(div, input);
    			input.checked = input.__value === /*$filters*/ ctx[4][/*filterType*/ ctx[3]];
    			append(div, t0);

    			if (label_slot_or_fallback) {
    				label_slot_or_fallback.m(div, null);
    			}

    			append(div, t1);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen(input, "change", /*input_change_handler*/ ctx[7]),
    					listen(input, "change", function () {
    						if (is_function(/*changeHandler*/ ctx[2])) /*changeHandler*/ ctx[2].apply(this, arguments);
    					})
    				];

    				mounted = true;
    			}
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;

    			if (!current || dirty & /*filterType*/ 8) {
    				attr(input, "name", /*filterType*/ ctx[3]);
    			}

    			if (!current || dirty & /*filterType, filterData*/ 10 && input_id_value !== (input_id_value = /*filterType*/ ctx[3] + /*id*/ ctx[9])) {
    				attr(input, "id", input_id_value);
    			}

    			if (!current || dirty & /*filterData*/ 2 && input_value_value !== (input_value_value = /*id*/ ctx[9])) {
    				input.__value = input_value_value;
    				input.value = input.__value;
    			}

    			if (dirty & /*$filters, filterType*/ 24) {
    				input.checked = input.__value === /*$filters*/ ctx[4][/*filterType*/ ctx[3]];
    			}

    			if (label_slot) {
    				if (label_slot.p && (!current || dirty & /*$$scope, filterData*/ 34)) {
    					update_slot_base(
    						label_slot,
    						label_slot_template,
    						ctx,
    						/*$$scope*/ ctx[5],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[5])
    						: get_slot_changes(label_slot_template, /*$$scope*/ ctx[5], dirty, get_label_slot_changes),
    						get_label_slot_context
    					);
    				}
    			} else {
    				if (label_slot_or_fallback && label_slot_or_fallback.p && (!current || dirty & /*filterType, filterData*/ 10)) {
    					label_slot_or_fallback.p(ctx, !current ? -1 : dirty);
    				}
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(label_slot_or_fallback, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(label_slot_or_fallback, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			/*$$binding_groups*/ ctx[8][0].splice(/*$$binding_groups*/ ctx[8][0].indexOf(input), 1);
    			if (label_slot_or_fallback) label_slot_or_fallback.d(detaching);
    			mounted = false;
    			run_all(dispose);
    		}
    	};
    }

    function create_fragment$l(ctx) {
    	let fieldset;
    	let legend;
    	let t0;
    	let t1;
    	let t2;
    	let div1;
    	let div0;
    	let current;
    	let each_value = Object.entries(/*filterData*/ ctx[1]);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$8(get_each_context$8(ctx, each_value, i));
    	}

    	const out = i => transition_out(each_blocks[i], 1, 1, () => {
    		each_blocks[i] = null;
    	});

    	return {
    		c() {
    			fieldset = element("fieldset");
    			legend = element("legend");
    			t0 = text(/*filterTitle*/ ctx[0]);
    			t1 = text(":");
    			t2 = space();
    			div1 = element("div");
    			div0 = element("div");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			attr(legend, "class", "filter-group__title-wrapper");
    			attr(div0, "class", "filter-group__filter-options");
    			attr(div1, "class", "filter-group__filter-options-wrapper");
    			attr(fieldset, "class", "filter-group");
    		},
    		m(target, anchor) {
    			insert(target, fieldset, anchor);
    			append(fieldset, legend);
    			append(legend, t0);
    			append(legend, t1);
    			append(fieldset, t2);
    			append(fieldset, div1);
    			append(div1, div0);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(div0, null);
    			}

    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if (!current || dirty & /*filterTitle*/ 1) set_data(t0, /*filterTitle*/ ctx[0]);

    			if (dirty & /*filterType, Object, filterData, $$scope, $filters, changeHandler*/ 62) {
    				each_value = Object.entries(/*filterData*/ ctx[1]);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$8(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    						transition_in(each_blocks[i], 1);
    					} else {
    						each_blocks[i] = create_each_block$8(child_ctx);
    						each_blocks[i].c();
    						transition_in(each_blocks[i], 1);
    						each_blocks[i].m(div0, null);
    					}
    				}

    				group_outros();

    				for (i = each_value.length; i < each_blocks.length; i += 1) {
    					out(i);
    				}

    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;

    			for (let i = 0; i < each_value.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			current = true;
    		},
    		o(local) {
    			each_blocks = each_blocks.filter(Boolean);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(fieldset);
    			destroy_each(each_blocks, detaching);
    		}
    	};
    }

    function instance$l($$self, $$props, $$invalidate) {
    	let $filters;
    	component_subscribe($$self, filters, $$value => $$invalidate(4, $filters = $$value));
    	let { $$slots: slots = {}, $$scope } = $$props;
    	let { filterTitle } = $$props;
    	let { filterData } = $$props;
    	let { changeHandler } = $$props;
    	let { filterType } = $$props;
    	const $$binding_groups = [[]];

    	function input_change_handler() {
    		$filters[filterType] = this.__value;
    		filters.set($filters);
    	}

    	$$self.$$set = $$props => {
    		if ('filterTitle' in $$props) $$invalidate(0, filterTitle = $$props.filterTitle);
    		if ('filterData' in $$props) $$invalidate(1, filterData = $$props.filterData);
    		if ('changeHandler' in $$props) $$invalidate(2, changeHandler = $$props.changeHandler);
    		if ('filterType' in $$props) $$invalidate(3, filterType = $$props.filterType);
    		if ('$$scope' in $$props) $$invalidate(5, $$scope = $$props.$$scope);
    	};

    	return [
    		filterTitle,
    		filterData,
    		changeHandler,
    		filterType,
    		$filters,
    		$$scope,
    		slots,
    		input_change_handler,
    		$$binding_groups
    	];
    }

    class FilterGroup extends SvelteComponent {
    	constructor(options) {
    		super();

    		init(this, options, instance$l, create_fragment$l, safe_not_equal, {
    			filterTitle: 0,
    			filterData: 1,
    			changeHandler: 2,
    			filterType: 3
    		});
    	}
    }

    /* src/Search/SearchFilters.svelte generated by Svelte v3.48.0 */

    function create_if_block$c(ctx) {
    	let div;
    	let filtergroup0;
    	let t0;
    	let filtergroup1;
    	let t1;
    	let filtergroup2;
    	let div_transition;
    	let current;

    	filtergroup0 = new FilterGroup({
    			props: {
    				filterTitle: window.Drupal.t('Development Status'),
    				filterData: DEVELOPMENT_OPTIONS,
    				filterType: "developmentStatus",
    				changeHandler: /*onAdvancedFilter*/ ctx[0],
    				$$slots: {
    					label: [
    						create_label_slot_2,
    						({ id, label }) => ({ 3: id, 4: label }),
    						({ id, label }) => (id ? 8 : 0) | (label ? 16 : 0)
    					]
    				},
    				$$scope: { ctx }
    			}
    		});

    	filtergroup1 = new FilterGroup({
    			props: {
    				filterTitle: window.Drupal.t('Maintenance Status'),
    				filterData: MAINTENANCE_OPTIONS,
    				filterType: "maintenanceStatus",
    				changeHandler: /*onAdvancedFilter*/ ctx[0],
    				$$slots: {
    					label: [
    						create_label_slot_1,
    						({ id, label }) => ({ 3: id, 4: label }),
    						({ id, label }) => (id ? 8 : 0) | (label ? 16 : 0)
    					]
    				},
    				$$scope: { ctx }
    			}
    		});

    	filtergroup2 = new FilterGroup({
    			props: {
    				filterTitle: window.Drupal.t('Security Advisory Coverage'),
    				filterData: SECURITY_OPTIONS,
    				filterType: "securityCoverage",
    				changeHandler: /*onAdvancedFilter*/ ctx[0],
    				$$slots: {
    					label: [
    						create_label_slot,
    						({ id, label }) => ({ 3: id, 4: label }),
    						({ id, label }) => (id ? 8 : 0) | (label ? 16 : 0)
    					]
    				},
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			div = element("div");
    			create_component(filtergroup0.$$.fragment);
    			t0 = space();
    			create_component(filtergroup1.$$.fragment);
    			t1 = space();
    			create_component(filtergroup2.$$.fragment);
    			attr(div, "class", "search__filters");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			mount_component(filtergroup0, div, null);
    			append(div, t0);
    			mount_component(filtergroup1, div, null);
    			append(div, t1);
    			mount_component(filtergroup2, div, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const filtergroup0_changes = {};
    			if (dirty & /*onAdvancedFilter*/ 1) filtergroup0_changes.changeHandler = /*onAdvancedFilter*/ ctx[0];

    			if (dirty & /*$$scope, id, label*/ 56) {
    				filtergroup0_changes.$$scope = { dirty, ctx };
    			}

    			filtergroup0.$set(filtergroup0_changes);
    			const filtergroup1_changes = {};
    			if (dirty & /*onAdvancedFilter*/ 1) filtergroup1_changes.changeHandler = /*onAdvancedFilter*/ ctx[0];

    			if (dirty & /*$$scope, id, label*/ 56) {
    				filtergroup1_changes.$$scope = { dirty, ctx };
    			}

    			filtergroup1.$set(filtergroup1_changes);
    			const filtergroup2_changes = {};
    			if (dirty & /*onAdvancedFilter*/ 1) filtergroup2_changes.changeHandler = /*onAdvancedFilter*/ ctx[0];

    			if (dirty & /*$$scope, id, label*/ 56) {
    				filtergroup2_changes.$$scope = { dirty, ctx };
    			}

    			filtergroup2.$set(filtergroup2_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(filtergroup0.$$.fragment, local);
    			transition_in(filtergroup1.$$.fragment, local);
    			transition_in(filtergroup2.$$.fragment, local);

    			add_render_callback(() => {
    				if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
    				div_transition.run(1);
    			});

    			current = true;
    		},
    		o(local) {
    			transition_out(filtergroup0.$$.fragment, local);
    			transition_out(filtergroup1.$$.fragment, local);
    			transition_out(filtergroup2.$$.fragment, local);
    			if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
    			div_transition.run(0);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			destroy_component(filtergroup0);
    			destroy_component(filtergroup1);
    			destroy_component(filtergroup2);
    			if (detaching && div_transition) div_transition.end();
    		}
    	};
    }

    // (27:6) 
    function create_label_slot_2(ctx) {
    	let label;
    	let t_value = /*label*/ ctx[4] + "";
    	let t;
    	let label_for_value;

    	return {
    		c() {
    			label = element("label");
    			t = text(t_value);
    			attr(label, "slot", "label");
    			attr(label, "class", "search__checkbox-label");
    			attr(label, "for", label_for_value = `developmentStatus${/*id*/ ctx[3]}`);
    		},
    		m(target, anchor) {
    			insert(target, label, anchor);
    			append(label, t);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*label*/ 16 && t_value !== (t_value = /*label*/ ctx[4] + "")) set_data(t, t_value);

    			if (dirty & /*id*/ 8 && label_for_value !== (label_for_value = `developmentStatus${/*id*/ ctx[3]}`)) {
    				attr(label, "for", label_for_value);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(label);
    		}
    	};
    }

    // (43:6) 
    function create_label_slot_1(ctx) {
    	let label;
    	let t_value = /*label*/ ctx[4] + "";
    	let t;
    	let label_for_value;

    	return {
    		c() {
    			label = element("label");
    			t = text(t_value);
    			attr(label, "slot", "label");
    			attr(label, "class", "search__checkbox-label");
    			attr(label, "for", label_for_value = `maintenanceStatus${/*id*/ ctx[3]}`);
    		},
    		m(target, anchor) {
    			insert(target, label, anchor);
    			append(label, t);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*label*/ 16 && t_value !== (t_value = /*label*/ ctx[4] + "")) set_data(t, t_value);

    			if (dirty & /*id*/ 8 && label_for_value !== (label_for_value = `maintenanceStatus${/*id*/ ctx[3]}`)) {
    				attr(label, "for", label_for_value);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(label);
    		}
    	};
    }

    // (65:8) {#if id === COVERED_ID}
    function create_if_block_1$b(ctx) {
    	let span;

    	return {
    		c() {
    			span = element("span");
    			attr(span, "class", "small-icons");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    		},
    		d(detaching) {
    			if (detaching) detach(span);
    		}
    	};
    }

    // (59:6) 
    function create_label_slot(ctx) {
    	let label;
    	let t0_value = /*label*/ ctx[4] + "";
    	let t0;
    	let t1;
    	let label_for_value;
    	let if_block = /*id*/ ctx[3] === COVERED_ID && create_if_block_1$b();

    	return {
    		c() {
    			label = element("label");
    			t0 = text(t0_value);
    			t1 = space();
    			if (if_block) if_block.c();
    			attr(label, "slot", "label");
    			attr(label, "class", "search__checkbox-label");
    			attr(label, "for", label_for_value = `securityCoverage${/*id*/ ctx[3]}`);
    		},
    		m(target, anchor) {
    			insert(target, label, anchor);
    			append(label, t0);
    			append(label, t1);
    			if (if_block) if_block.m(label, null);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*label*/ 16 && t0_value !== (t0_value = /*label*/ ctx[4] + "")) set_data(t0, t0_value);

    			if (/*id*/ ctx[3] === COVERED_ID) {
    				if (if_block) ; else {
    					if_block = create_if_block_1$b();
    					if_block.c();
    					if_block.m(label, null);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}

    			if (dirty & /*id*/ 8 && label_for_value !== (label_for_value = `securityCoverage${/*id*/ ctx[3]}`)) {
    				attr(label, "for", label_for_value);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(label);
    			if (if_block) if_block.d();
    		}
    	};
    }

    function create_fragment$k(ctx) {
    	let if_block_anchor;
    	let current;
    	let if_block = /*isOpen*/ ctx[1] && create_if_block$c(ctx);

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if (/*isOpen*/ ctx[1]) {
    				if (if_block) {
    					if_block.p(ctx, dirty);

    					if (dirty & /*isOpen*/ 2) {
    						transition_in(if_block, 1);
    					}
    				} else {
    					if_block = create_if_block$c(ctx);
    					if_block.c();
    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				group_outros();

    				transition_out(if_block, 1, 1, () => {
    					if_block = null;
    				});

    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    function instance$k($$self, $$props, $$invalidate) {
    	let { onAdvancedFilter } = $$props;
    	let { isOpen } = $$props;

    	$$self.$$set = $$props => {
    		if ('onAdvancedFilter' in $$props) $$invalidate(0, onAdvancedFilter = $$props.onAdvancedFilter);
    		if ('isOpen' in $$props) $$invalidate(1, isOpen = $$props.isOpen);
    	};

    	return [onAdvancedFilter, isOpen];
    }

    class SearchFilters extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$k, create_fragment$k, safe_not_equal, { onAdvancedFilter: 0, isOpen: 1 });
    	}
    }

    /* src/Search/SearchFilterToggle.svelte generated by Svelte v3.48.0 */

    function create_fragment$j(ctx) {
    	let div;
    	let section;
    	let button;
    	let img;
    	let img_src_value;
    	let t;
    	let button_aria_label_value;
    	let button_aria_expanded_value;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			div = element("div");
    			section = element("section");
    			button = element("button");
    			img = element("img");
    			t = text("Filters");
    			if (!src_url_equal(img.src, img_src_value = "" + (FULL_MODULE_PATH + "/images/advanced-filter-icon.svg"))) attr(img, "src", img_src_value);
    			attr(img, "alt", "advanced filter icon");
    			attr(img, "class", "search__filter__toggle-img");
    			attr(button, "type", "button");
    			attr(button, "class", "search__filter__toggle form-element");
    			attr(button, "aria-controls", "filter-dropdown");

    			attr(button, "aria-label", button_aria_label_value = /*isOpen*/ ctx[0]
    			? window.Drupal.t('Close Filter')
    			: window.Drupal.t('Open Filter'));

    			attr(button, "aria-expanded", button_aria_expanded_value = /*isOpen*/ ctx[0].toString());
    			toggle_class(button, "is_open", /*isOpen*/ ctx[0]);
    			attr(section, "aria-label", window.Drupal.t('Filter settings'));
    			attr(div, "class", "search__filter__toggle-container");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			append(div, section);
    			append(section, button);
    			append(button, img);
    			append(button, t);

    			if (!mounted) {
    				dispose = listen(button, "click", /*click_handler*/ ctx[2]);
    				mounted = true;
    			}
    		},
    		p(ctx, [dirty]) {
    			if (dirty & /*isOpen*/ 1 && button_aria_label_value !== (button_aria_label_value = /*isOpen*/ ctx[0]
    			? window.Drupal.t('Close Filter')
    			: window.Drupal.t('Open Filter'))) {
    				attr(button, "aria-label", button_aria_label_value);
    			}

    			if (dirty & /*isOpen*/ 1 && button_aria_expanded_value !== (button_aria_expanded_value = /*isOpen*/ ctx[0].toString())) {
    				attr(button, "aria-expanded", button_aria_expanded_value);
    			}

    			if (dirty & /*isOpen*/ 1) {
    				toggle_class(button, "is_open", /*isOpen*/ ctx[0]);
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(div);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    function instance$j($$self, $$props, $$invalidate) {
    	let { isOpen } = $$props;

    	/* When the user clicks on the button,
     toggle between hiding and showing the dropdown content */
    	function openDropdown() {
    		$$invalidate(0, isOpen = !isOpen);
    	}

    	const click_handler = () => openDropdown();

    	$$self.$$set = $$props => {
    		if ('isOpen' in $$props) $$invalidate(0, isOpen = $$props.isOpen);
    	};

    	return [isOpen, openDropdown, click_handler];
    }

    class SearchFilterToggle extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$j, create_fragment$j, safe_not_equal, { isOpen: 0 });
    	}
    }

    /* src/Search/SearchSort.svelte generated by Svelte v3.48.0 */

    function get_each_context$7(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[9] = list[i];
    	return child_ctx;
    }

    // (40:4) {#each $sortCriteria as opt}
    function create_each_block$7(ctx) {
    	let option;
    	let t0_value = /*opt*/ ctx[9].text + "";
    	let t0;
    	let t1;
    	let option_value_value;

    	return {
    		c() {
    			option = element("option");
    			t0 = text(t0_value);
    			t1 = space();
    			option.__value = option_value_value = /*opt*/ ctx[9].id;
    			option.value = option.__value;
    		},
    		m(target, anchor) {
    			insert(target, option, anchor);
    			append(option, t0);
    			append(option, t1);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*$sortCriteria*/ 2 && t0_value !== (t0_value = /*opt*/ ctx[9].text + "")) set_data(t0, t0_value);

    			if (dirty & /*$sortCriteria*/ 2 && option_value_value !== (option_value_value = /*opt*/ ctx[9].id)) {
    				option.__value = option_value_value;
    				option.value = option.__value;
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(option);
    		}
    	};
    }

    function create_fragment$i(ctx) {
    	let div;
    	let label;
    	let t1;
    	let select;
    	let mounted;
    	let dispose;
    	let each_value = /*$sortCriteria*/ ctx[1];
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$7(get_each_context$7(ctx, each_value, i));
    	}

    	return {
    		c() {
    			div = element("div");
    			label = element("label");
    			label.textContent = `${window.Drupal.t('Sort by:')}`;
    			t1 = space();
    			select = element("select");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			attr(label, "for", "pb-sort");
    			attr(select, "name", "pb-sort");
    			attr(select, "id", "pb-sort");
    			attr(select, "class", "search__sort-select form-select form-element form-element--type-select");
    			if (/*$sort*/ ctx[0] === void 0) add_render_callback(() => /*select_change_handler*/ ctx[5].call(select));
    			attr(div, "class", "search__sort");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			append(div, label);
    			append(div, t1);
    			append(div, select);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(select, null);
    			}

    			select_option(select, /*$sort*/ ctx[0]);

    			if (!mounted) {
    				dispose = [
    					listen(select, "change", /*select_change_handler*/ ctx[5]),
    					listen(select, "change", /*onSort*/ ctx[2])
    				];

    				mounted = true;
    			}
    		},
    		p(ctx, [dirty]) {
    			if (dirty & /*$sortCriteria*/ 2) {
    				each_value = /*$sortCriteria*/ ctx[1];
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$7(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$7(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(select, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}

    			if (dirty & /*$sort, $sortCriteria*/ 3) {
    				select_option(select, /*$sort*/ ctx[0]);
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(div);
    			destroy_each(each_blocks, detaching);
    			mounted = false;
    			run_all(dispose);
    		}
    	};
    }

    function instance$i($$self, $$props, $$invalidate) {
    	let $sort;
    	let $sortCriteria;
    	component_subscribe($$self, sort, $$value => $$invalidate(0, $sort = $$value));
    	component_subscribe($$self, sortCriteria, $$value => $$invalidate(1, $sortCriteria = $$value));
    	let { sortText } = $$props;
    	let { refresh } = $$props;
    	const dispatch = createEventDispatcher();
    	const stateContext = getContext('state');

    	async function onSort(event) {
    		const state = stateContext.getState();

    		const detail = {
    			originalEvent: event,
    			page: state.page,
    			pageIndex: state.pageIndex,
    			pageSize: state.pageSize,
    			rows: state.filteredRows,
    			sort: $sort
    		};

    		dispatch('sort', detail);
    		stateContext.setPage(0, 0);
    		stateContext.setRows(detail.rows);
    		$$invalidate(3, sortText = $sortCriteria.find(option => option.id === $sort).text);
    		refresh();
    	}

    	function select_change_handler() {
    		$sort = select_value(this);
    		sort.set($sort);
    	}

    	$$self.$$set = $$props => {
    		if ('sortText' in $$props) $$invalidate(3, sortText = $$props.sortText);
    		if ('refresh' in $$props) $$invalidate(4, refresh = $$props.refresh);
    	};

    	return [$sort, $sortCriteria, onSort, sortText, refresh, select_change_handler];
    }

    class SearchSort extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$i, create_fragment$i, safe_not_equal, { sortText: 3, refresh: 4 });
    	}
    }

    /* src/Search/Search.svelte generated by Svelte v3.48.0 */

    function get_each_context$6(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[35] = list[i];
    	return child_ctx;
    }

    function get_each_context_1$2(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[38] = list[i];
    	return child_ctx;
    }

    // (190:6) {#if $searchString}
    function create_if_block_3$4(ctx) {
    	let button;
    	let img;
    	let img_src_value;
    	let button_aria_label_value;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			button = element("button");
    			img = element("img");
    			if (!src_url_equal(img.src, img_src_value = "" + (FULL_MODULE_PATH + "/images/cross" + (DARK_COLOR_SCHEME ? '--dark-color-scheme' : '') + ".svg"))) attr(img, "src", img_src_value);
    			attr(img, "alt", "");
    			attr(button, "class", "search__search-clear");
    			attr(button, "id", "clear-text");
    			attr(button, "type", "button");
    			attr(button, "aria-label", button_aria_label_value = window.Drupal.t('Clear search text'));
    			attr(button, "tabindex", "-1");
    		},
    		m(target, anchor) {
    			insert(target, button, anchor);
    			append(button, img);

    			if (!mounted) {
    				dispose = listen(button, "click", /*clearText*/ ctx[14]);
    				mounted = true;
    			}
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(button);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (227:10) {#if $filters[filterType]}
    function create_if_block_2$6(ctx) {
    	let filterapplied;
    	let current;

    	function func() {
    		return /*func*/ ctx[22](/*filterType*/ ctx[38]);
    	}

    	filterapplied = new FilterApplied({
    			props: {
    				id: /*$filters*/ ctx[6][/*filterType*/ ctx[38]],
    				label: /*$filtersVocabularies*/ ctx[8][/*filterType*/ ctx[38]][/*$filters*/ ctx[6][/*filterType*/ ctx[38]]],
    				clickHandler: func
    			}
    		});

    	return {
    		c() {
    			create_component(filterapplied.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(filterapplied, target, anchor);
    			current = true;
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			const filterapplied_changes = {};
    			if (dirty[0] & /*$filters*/ 64) filterapplied_changes.id = /*$filters*/ ctx[6][/*filterType*/ ctx[38]];
    			if (dirty[0] & /*$filtersVocabularies, $filters*/ 320) filterapplied_changes.label = /*$filtersVocabularies*/ ctx[8][/*filterType*/ ctx[38]][/*$filters*/ ctx[6][/*filterType*/ ctx[38]]];
    			filterapplied.$set(filterapplied_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(filterapplied.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(filterapplied.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(filterapplied, detaching);
    		}
    	};
    }

    // (226:8) {#each ['developmentStatus', 'maintenanceStatus', 'securityCoverage'] as filterType}
    function create_each_block_1$2(ctx) {
    	let if_block_anchor;
    	let current;
    	let if_block = /*$filters*/ ctx[6][/*filterType*/ ctx[38]] && create_if_block_2$6(ctx);

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if (/*$filters*/ ctx[6][/*filterType*/ ctx[38]]) {
    				if (if_block) {
    					if_block.p(ctx, dirty);

    					if (dirty[0] & /*$filters*/ 64) {
    						transition_in(if_block, 1);
    					}
    				} else {
    					if_block = create_if_block_2$6(ctx);
    					if_block.c();
    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				group_outros();

    				transition_out(if_block, 1, 1, () => {
    					if_block = null;
    				});

    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (236:8) {#each $moduleCategoryFilter as category}
    function create_each_block$6(ctx) {
    	let filterapplied;
    	let current;

    	function func_1() {
    		return /*func_1*/ ctx[23](/*category*/ ctx[35]);
    	}

    	filterapplied = new FilterApplied({
    			props: {
    				id: /*category*/ ctx[35],
    				label: /*$moduleCategoryVocabularies*/ ctx[9][/*category*/ ctx[35]],
    				clickHandler: func_1
    			}
    		});

    	return {
    		c() {
    			create_component(filterapplied.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(filterapplied, target, anchor);
    			current = true;
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			const filterapplied_changes = {};
    			if (dirty[0] & /*$moduleCategoryFilter*/ 32) filterapplied_changes.id = /*category*/ ctx[35];
    			if (dirty[0] & /*$moduleCategoryVocabularies, $moduleCategoryFilter*/ 544) filterapplied_changes.label = /*$moduleCategoryVocabularies*/ ctx[9][/*category*/ ctx[35]];
    			if (dirty[0] & /*$moduleCategoryFilter*/ 32) filterapplied_changes.clickHandler = func_1;
    			filterapplied.$set(filterapplied_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(filterapplied.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(filterapplied.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(filterapplied, detaching);
    		}
    	};
    }

    // (251:8) {#if $filters.securityCoverage !== ALL_VALUES_ID || $filters.maintenanceStatus !== ALL_VALUES_ID || $filters.developmentStatus !== ALL_VALUES_ID || $moduleCategoryFilter.length}
    function create_if_block_1$a(ctx) {
    	let button;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			button = element("button");
    			button.textContent = `${window.Drupal.t('Clear filters')}`;
    			attr(button, "class", "search__filter-button");
    			attr(button, "type", "button");
    		},
    		m(target, anchor) {
    			insert(target, button, anchor);

    			if (!mounted) {
    				dispose = listen(button, "click", prevent_default(/*click_handler*/ ctx[24]));
    				mounted = true;
    			}
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(button);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (261:8) {#if !($filters.maintenanceStatus === ACTIVELY_MAINTAINED_ID && $filters.securityCoverage === COVERED_ID && $filters.developmentStatus === ALL_VALUES_ID && $moduleCategoryFilter.length === 0)}
    function create_if_block$b(ctx) {
    	let button;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			button = element("button");
    			button.textContent = `${window.Drupal.t('Recommended filters')}`;
    			attr(button, "class", "search__filter-button");
    			attr(button, "type", "button");
    		},
    		m(target, anchor) {
    			insert(target, button, anchor);

    			if (!mounted) {
    				dispose = listen(button, "click", prevent_default(/*click_handler_1*/ ctx[25]));
    				mounted = true;
    			}
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(button);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    function create_fragment$h(ctx) {
    	let form;
    	let div1;
    	let label;
    	let t1;
    	let div0;
    	let input;
    	let input_title_value;
    	let input_placeholder_value;
    	let t2;
    	let t3;
    	let img;
    	let img_src_value;
    	let t4;
    	let div3;
    	let section;
    	let searchfiltertoggle;
    	let updating_isOpen;
    	let t5;
    	let div2;
    	let t6;
    	let t7;
    	let t8;
    	let t9;
    	let searchsort;
    	let updating_sortText;
    	let t10;
    	let div4;
    	let searchfilters;
    	let updating_isOpen_1;
    	let current;
    	let mounted;
    	let dispose;
    	let if_block0 = /*$searchString*/ ctx[7] && create_if_block_3$4(ctx);

    	function searchfiltertoggle_isOpen_binding(value) {
    		/*searchfiltertoggle_isOpen_binding*/ ctx[21](value);
    	}

    	let searchfiltertoggle_props = {};

    	if (/*filtersOpen*/ ctx[3] !== void 0) {
    		searchfiltertoggle_props.isOpen = /*filtersOpen*/ ctx[3];
    	}

    	searchfiltertoggle = new SearchFilterToggle({ props: searchfiltertoggle_props });
    	binding_callbacks.push(() => bind(searchfiltertoggle, 'isOpen', searchfiltertoggle_isOpen_binding));
    	let each_value_1 = ['developmentStatus', 'maintenanceStatus', 'securityCoverage'];
    	let each_blocks_1 = [];

    	for (let i = 0; i < 3; i += 1) {
    		each_blocks_1[i] = create_each_block_1$2(get_each_context_1$2(ctx, each_value_1, i));
    	}

    	const out = i => transition_out(each_blocks_1[i], 1, 1, () => {
    		each_blocks_1[i] = null;
    	});

    	let each_value = /*$moduleCategoryFilter*/ ctx[5];
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$6(get_each_context$6(ctx, each_value, i));
    	}

    	const out_1 = i => transition_out(each_blocks[i], 1, 1, () => {
    		each_blocks[i] = null;
    	});

    	let if_block1 = (/*$filters*/ ctx[6].securityCoverage !== ALL_VALUES_ID || /*$filters*/ ctx[6].maintenanceStatus !== ALL_VALUES_ID || /*$filters*/ ctx[6].developmentStatus !== ALL_VALUES_ID || /*$moduleCategoryFilter*/ ctx[5].length) && create_if_block_1$a(ctx);
    	let if_block2 = !(/*$filters*/ ctx[6].maintenanceStatus === ACTIVELY_MAINTAINED_ID && /*$filters*/ ctx[6].securityCoverage === COVERED_ID && /*$filters*/ ctx[6].developmentStatus === ALL_VALUES_ID && /*$moduleCategoryFilter*/ ctx[5].length === 0) && create_if_block$b(ctx);

    	function searchsort_sortText_binding(value) {
    		/*searchsort_sortText_binding*/ ctx[26](value);
    	}

    	let searchsort_props = { refresh: /*refreshLiveRegion*/ ctx[0] };

    	if (/*sortText*/ ctx[4] !== void 0) {
    		searchsort_props.sortText = /*sortText*/ ctx[4];
    	}

    	searchsort = new SearchSort({ props: searchsort_props });
    	binding_callbacks.push(() => bind(searchsort, 'sortText', searchsort_sortText_binding));
    	searchsort.$on("sort", /*sort_handler*/ ctx[27]);

    	function searchfilters_isOpen_binding(value) {
    		/*searchfilters_isOpen_binding*/ ctx[28](value);
    	}

    	let searchfilters_props = {
    		onAdvancedFilter: /*onAdvancedFilter*/ ctx[11]
    	};

    	if (/*filtersOpen*/ ctx[3] !== void 0) {
    		searchfilters_props.isOpen = /*filtersOpen*/ ctx[3];
    	}

    	searchfilters = new SearchFilters({ props: searchfilters_props });
    	binding_callbacks.push(() => bind(searchfilters, 'isOpen', searchfilters_isOpen_binding));

    	return {
    		c() {
    			form = element("form");
    			div1 = element("div");
    			label = element("label");
    			label.textContent = `${window.Drupal.t('Search for modules')}`;
    			t1 = space();
    			div0 = element("div");
    			input = element("input");
    			t2 = space();
    			if (if_block0) if_block0.c();
    			t3 = space();
    			img = element("img");
    			t4 = space();
    			div3 = element("div");
    			section = element("section");
    			create_component(searchfiltertoggle.$$.fragment);
    			t5 = space();
    			div2 = element("div");

    			for (let i = 0; i < 3; i += 1) {
    				each_blocks_1[i].c();
    			}

    			t6 = space();

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t7 = space();
    			if (if_block1) if_block1.c();
    			t8 = space();
    			if (if_block2) if_block2.c();
    			t9 = space();
    			create_component(searchsort.$$.fragment);
    			t10 = space();
    			div4 = element("div");
    			create_component(searchfilters.$$.fragment);
    			attr(label, "for", "pb-text");
    			attr(label, "class", "form-item__label");
    			attr(input, "class", "search__search_term form-text form-element form-element--type-text");
    			attr(input, "type", "search");
    			attr(input, "title", input_title_value = /*labels*/ ctx[1].placeholder);
    			attr(input, "placeholder", input_placeholder_value = /*labels*/ ctx[1].placeholder);
    			attr(input, "id", "pb-text");
    			attr(input, "name", "text");
    			attr(img, "class", "search__search-icon");
    			attr(img, "id", "search-icon");
    			if (!src_url_equal(img.src, img_src_value = "" + (FULL_MODULE_PATH + "/images/search-icon" + (DARK_COLOR_SCHEME ? '--dark-color-scheme' : '') + ".svg"))) attr(img, "src", img_src_value);
    			attr(img, "alt", "");
    			attr(div0, "class", "search__search-bar");
    			attr(div1, "class", "search__form-item js-form-item form-item js-form-type-textfield form-type--textfield");
    			attr(div1, "role", "search");
    			attr(div2, "class", "search__results-count");
    			attr(section, "class", "search__filter-wrapper");
    			attr(section, "aria-label", window.Drupal.t('Search results'));
    			attr(div3, "class", "search__grid-container js-form-item js-form-type-select form-type--select js-form-item-type form-item--type");
    			attr(div4, "class", "search__dropdown dropdown-filters");
    			attr(div4, "id", "filter-dropdown");
    			attr(form, "class", "search__form");
    		},
    		m(target, anchor) {
    			insert(target, form, anchor);
    			append(form, div1);
    			append(div1, label);
    			append(div1, t1);
    			append(div1, div0);
    			append(div0, input);
    			set_input_value(input, /*$searchString*/ ctx[7]);
    			append(div0, t2);
    			if (if_block0) if_block0.m(div0, null);
    			append(div0, t3);
    			append(div0, img);
    			append(form, t4);
    			append(form, div3);
    			append(div3, section);
    			mount_component(searchfiltertoggle, section, null);
    			append(section, t5);
    			append(section, div2);

    			for (let i = 0; i < 3; i += 1) {
    				each_blocks_1[i].m(div2, null);
    			}

    			append(div2, t6);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(div2, null);
    			}

    			append(div2, t7);
    			if (if_block1) if_block1.m(div2, null);
    			append(div2, t8);
    			if (if_block2) if_block2.m(div2, null);
    			append(div3, t9);
    			mount_component(searchsort, div3, null);
    			append(form, t10);
    			append(form, div4);
    			mount_component(searchfilters, div4, null);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen(input, "input", /*input_input_handler*/ ctx[19]),
    					listen(input, "keyup", /*Drupal*/ ctx[10].debounce(/*onSearch*/ ctx[2], 250, false)),
    					listen(input, "keydown", /*keydown_handler*/ ctx[20])
    				];

    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			if (!current || dirty[0] & /*labels*/ 2 && input_title_value !== (input_title_value = /*labels*/ ctx[1].placeholder)) {
    				attr(input, "title", input_title_value);
    			}

    			if (!current || dirty[0] & /*labels*/ 2 && input_placeholder_value !== (input_placeholder_value = /*labels*/ ctx[1].placeholder)) {
    				attr(input, "placeholder", input_placeholder_value);
    			}

    			if (dirty[0] & /*$searchString*/ 128) {
    				set_input_value(input, /*$searchString*/ ctx[7]);
    			}

    			if (/*$searchString*/ ctx[7]) {
    				if (if_block0) {
    					if_block0.p(ctx, dirty);
    				} else {
    					if_block0 = create_if_block_3$4(ctx);
    					if_block0.c();
    					if_block0.m(div0, t3);
    				}
    			} else if (if_block0) {
    				if_block0.d(1);
    				if_block0 = null;
    			}

    			const searchfiltertoggle_changes = {};

    			if (!updating_isOpen && dirty[0] & /*filtersOpen*/ 8) {
    				updating_isOpen = true;
    				searchfiltertoggle_changes.isOpen = /*filtersOpen*/ ctx[3];
    				add_flush_callback(() => updating_isOpen = false);
    			}

    			searchfiltertoggle.$set(searchfiltertoggle_changes);

    			if (dirty[0] & /*$filters, $filtersVocabularies, removeFilter*/ 8512) {
    				each_value_1 = ['developmentStatus', 'maintenanceStatus', 'securityCoverage'];
    				let i;

    				for (i = 0; i < 3; i += 1) {
    					const child_ctx = get_each_context_1$2(ctx, each_value_1, i);

    					if (each_blocks_1[i]) {
    						each_blocks_1[i].p(child_ctx, dirty);
    						transition_in(each_blocks_1[i], 1);
    					} else {
    						each_blocks_1[i] = create_each_block_1$2(child_ctx);
    						each_blocks_1[i].c();
    						transition_in(each_blocks_1[i], 1);
    						each_blocks_1[i].m(div2, t6);
    					}
    				}

    				group_outros();

    				for (i = 3; i < 3; i += 1) {
    					out(i);
    				}

    				check_outros();
    			}

    			if (dirty[0] & /*$moduleCategoryFilter, $moduleCategoryVocabularies, onSelectCategory*/ 4640) {
    				each_value = /*$moduleCategoryFilter*/ ctx[5];
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$6(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    						transition_in(each_blocks[i], 1);
    					} else {
    						each_blocks[i] = create_each_block$6(child_ctx);
    						each_blocks[i].c();
    						transition_in(each_blocks[i], 1);
    						each_blocks[i].m(div2, t7);
    					}
    				}

    				group_outros();

    				for (i = each_value.length; i < each_blocks.length; i += 1) {
    					out_1(i);
    				}

    				check_outros();
    			}

    			if (/*$filters*/ ctx[6].securityCoverage !== ALL_VALUES_ID || /*$filters*/ ctx[6].maintenanceStatus !== ALL_VALUES_ID || /*$filters*/ ctx[6].developmentStatus !== ALL_VALUES_ID || /*$moduleCategoryFilter*/ ctx[5].length) {
    				if (if_block1) {
    					if_block1.p(ctx, dirty);
    				} else {
    					if_block1 = create_if_block_1$a(ctx);
    					if_block1.c();
    					if_block1.m(div2, t8);
    				}
    			} else if (if_block1) {
    				if_block1.d(1);
    				if_block1 = null;
    			}

    			if (!(/*$filters*/ ctx[6].maintenanceStatus === ACTIVELY_MAINTAINED_ID && /*$filters*/ ctx[6].securityCoverage === COVERED_ID && /*$filters*/ ctx[6].developmentStatus === ALL_VALUES_ID && /*$moduleCategoryFilter*/ ctx[5].length === 0)) {
    				if (if_block2) {
    					if_block2.p(ctx, dirty);
    				} else {
    					if_block2 = create_if_block$b(ctx);
    					if_block2.c();
    					if_block2.m(div2, null);
    				}
    			} else if (if_block2) {
    				if_block2.d(1);
    				if_block2 = null;
    			}

    			const searchsort_changes = {};
    			if (dirty[0] & /*refreshLiveRegion*/ 1) searchsort_changes.refresh = /*refreshLiveRegion*/ ctx[0];

    			if (!updating_sortText && dirty[0] & /*sortText*/ 16) {
    				updating_sortText = true;
    				searchsort_changes.sortText = /*sortText*/ ctx[4];
    				add_flush_callback(() => updating_sortText = false);
    			}

    			searchsort.$set(searchsort_changes);
    			const searchfilters_changes = {};

    			if (!updating_isOpen_1 && dirty[0] & /*filtersOpen*/ 8) {
    				updating_isOpen_1 = true;
    				searchfilters_changes.isOpen = /*filtersOpen*/ ctx[3];
    				add_flush_callback(() => updating_isOpen_1 = false);
    			}

    			searchfilters.$set(searchfilters_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(searchfiltertoggle.$$.fragment, local);

    			for (let i = 0; i < 3; i += 1) {
    				transition_in(each_blocks_1[i]);
    			}

    			for (let i = 0; i < each_value.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			transition_in(searchsort.$$.fragment, local);
    			transition_in(searchfilters.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(searchfiltertoggle.$$.fragment, local);
    			each_blocks_1 = each_blocks_1.filter(Boolean);

    			for (let i = 0; i < 3; i += 1) {
    				transition_out(each_blocks_1[i]);
    			}

    			each_blocks = each_blocks.filter(Boolean);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			transition_out(searchsort.$$.fragment, local);
    			transition_out(searchfilters.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(form);
    			if (if_block0) if_block0.d();
    			destroy_component(searchfiltertoggle);
    			destroy_each(each_blocks_1, detaching);
    			destroy_each(each_blocks, detaching);
    			if (if_block1) if_block1.d();
    			if (if_block2) if_block2.d();
    			destroy_component(searchsort);
    			destroy_component(searchfilters);
    			mounted = false;
    			run_all(dispose);
    		}
    	};
    }

    function instance$h($$self, $$props, $$invalidate) {
    	let $moduleCategoryFilter;
    	let $filters;
    	let $searchString;
    	let $filtersVocabularies;
    	let $sort;
    	let $sortCriteria;
    	let $moduleCategoryVocabularies;
    	component_subscribe($$self, moduleCategoryFilter, $$value => $$invalidate(5, $moduleCategoryFilter = $$value));
    	component_subscribe($$self, filters, $$value => $$invalidate(6, $filters = $$value));
    	component_subscribe($$self, searchString, $$value => $$invalidate(7, $searchString = $$value));
    	component_subscribe($$self, filtersVocabularies, $$value => $$invalidate(8, $filtersVocabularies = $$value));
    	component_subscribe($$self, sort, $$value => $$invalidate(30, $sort = $$value));
    	component_subscribe($$self, sortCriteria, $$value => $$invalidate(31, $sortCriteria = $$value));
    	component_subscribe($$self, moduleCategoryVocabularies, $$value => $$invalidate(9, $moduleCategoryVocabularies = $$value));
    	const { Drupal } = window;
    	const dispatch = createEventDispatcher();
    	const stateContext = getContext('state');
    	let { refreshLiveRegion } = $$props;
    	const filter = (row, text) => Object.values(row).filter(item => item && item.toString().toLowerCase().indexOf(text.toLowerCase()) > 1).length > 0;
    	let { index = -1 } = $$props;
    	let { searchText } = $$props;

    	searchString.subscribe(value => {
    		$$invalidate(16, searchText = value);
    	});

    	let { labels = {
    		placeholder: window.Drupal.t('Module Name, Keyword(s), etc.')
    	} } = $$props;

    	// eslint-disable-next-line prefer-const
    	let filtersOpen = false;

    	let sortMatch = $sortCriteria.find(option => option.id === $sort);

    	if (typeof sortMatch === 'undefined') {
    		set_store_value(sort, $sort = $sortCriteria[0].id, $sort);
    		sortMatch = $sortCriteria.find(option => option.id === $sort);
    	}

    	let sortText = sortMatch.text;

    	const updateVocabularies = (vocabulary, value) => {
    		const normalizedValue = normalizeOptions(value);
    		const storedValue = JSON.parse(localStorage.getItem(`pb.${vocabulary}`));

    		if (storedValue === null || !shallowCompare(normalizedValue, storedValue)) {
    			set_store_value(filtersVocabularies, $filtersVocabularies[vocabulary] = normalizedValue, $filtersVocabularies);
    			localStorage.setItem(`pb.${vocabulary}`, JSON.stringify(normalizedValue));
    		}
    	};

    	onMount(() => {
    		updateVocabularies('developmentStatus', DEVELOPMENT_OPTIONS);
    		updateVocabularies('maintenanceStatus', MAINTENANCE_OPTIONS);
    		updateVocabularies('securityCoverage', SECURITY_OPTIONS);
    	});

    	async function onSearch(event) {
    		const state = stateContext.getState();

    		const detail = {
    			originalEvent: event,
    			filter,
    			index,
    			searchText,
    			page: state.page,
    			pageIndex: state.pageIndex,
    			pageSize: state.pageSize,
    			rows: state.filteredRows
    		};

    		dispatch('search', detail);

    		if (detail.preventDefault !== true) {
    			if (detail.searchText.length === 0) {
    				stateContext.setRows(state.rows);
    			} else {
    				stateContext.setRows(detail.rows.filter(r => detail.filter(r, detail.searchText, index)));
    			}

    			stateContext.setPage(0, 0);
    		} else {
    			stateContext.setRows(detail.rows);
    		}

    		refreshLiveRegion();
    	}

    	const onAdvancedFilter = async event => {
    		const state = stateContext.getState();

    		const detail = {
    			originalEvent: event,
    			developmentStatus: $filters.developmentStatus,
    			maintenanceStatus: $filters.maintenanceStatus,
    			securityCoverage: $filters.securityCoverage,
    			page: state.page,
    			pageIndex: state.pageIndex,
    			pageSize: state.pageSize,
    			rows: state.filteredRows
    		};

    		dispatch('advancedFilter', detail);
    		stateContext.setPage(0, 0);
    		stateContext.setRows(detail.rows);
    		refreshLiveRegion();
    	};

    	function onSelectCategory(event) {
    		const state = stateContext.getState();

    		const detail = {
    			originalEvent: event,
    			category: $moduleCategoryFilter,
    			page: state.page,
    			pageIndex: state.pageIndex,
    			pageSize: state.pageSize,
    			rows: state.filteredRows
    		};

    		dispatch('selectCategory', detail);
    		stateContext.setPage(0, 0);
    		stateContext.setRows(detail.rows);
    	}

    	function removeFilter(filterType) {
    		set_store_value(filters, $filters[filterType] = ALL_VALUES_ID, $filters);
    		filters.set($filters);
    		onAdvancedFilter();
    	}

    	function clearText() {
    		set_store_value(searchString, $searchString = '', $searchString);
    		onSearch();
    		document.getElementById('pb-text').focus();
    	}

    	/**
     * Actions performed when clicking filter resets such as "recommended"
     * @param {string} maintenanceId
     *    ID of the selected maintenance status.
     * @param {string} developmentId
     *   ID of the selected development status.
     * @param {string} securityId
     *   ID of the selected security status.
     */
    	const filterResets = (maintenanceId, developmentId, securityId) => {
    		set_store_value(filters, $filters.maintenanceStatus = maintenanceId, $filters);
    		set_store_value(filters, $filters.developmentStatus = developmentId, $filters);
    		set_store_value(filters, $filters.securityCoverage = securityId, $filters);
    		filters.set($filters);
    		set_store_value(moduleCategoryFilter, $moduleCategoryFilter = [], $moduleCategoryFilter);
    		onAdvancedFilter();
    		onSelectCategory();
    	};

    	function input_input_handler() {
    		$searchString = this.value;
    		searchString.set($searchString);
    	}

    	const keydown_handler = e => {
    		if (e.key === 'Escape') {
    			e.preventDefault();
    			clearText();
    		}
    	};

    	function searchfiltertoggle_isOpen_binding(value) {
    		filtersOpen = value;
    		$$invalidate(3, filtersOpen);
    	}

    	const func = filterType => removeFilter(filterType);

    	const func_1 = category => {
    		$moduleCategoryFilter.splice($moduleCategoryFilter.indexOf(category), 1);
    		moduleCategoryFilter.set($moduleCategoryFilter);
    		onSelectCategory();
    	};

    	const click_handler = () => filterResets(ALL_VALUES_ID, ALL_VALUES_ID, ALL_VALUES_ID);
    	const click_handler_1 = () => filterResets(ACTIVELY_MAINTAINED_ID, ALL_VALUES_ID, COVERED_ID);

    	function searchsort_sortText_binding(value) {
    		sortText = value;
    		$$invalidate(4, sortText);
    	}

    	function sort_handler(event) {
    		bubble.call(this, $$self, event);
    	}

    	function searchfilters_isOpen_binding(value) {
    		filtersOpen = value;
    		$$invalidate(3, filtersOpen);
    	}

    	$$self.$$set = $$props => {
    		if ('refreshLiveRegion' in $$props) $$invalidate(0, refreshLiveRegion = $$props.refreshLiveRegion);
    		if ('index' in $$props) $$invalidate(18, index = $$props.index);
    		if ('searchText' in $$props) $$invalidate(16, searchText = $$props.searchText);
    		if ('labels' in $$props) $$invalidate(1, labels = $$props.labels);
    	};

    	return [
    		refreshLiveRegion,
    		labels,
    		onSearch,
    		filtersOpen,
    		sortText,
    		$moduleCategoryFilter,
    		$filters,
    		$searchString,
    		$filtersVocabularies,
    		$moduleCategoryVocabularies,
    		Drupal,
    		onAdvancedFilter,
    		onSelectCategory,
    		removeFilter,
    		clearText,
    		filterResets,
    		searchText,
    		filter,
    		index,
    		input_input_handler,
    		keydown_handler,
    		searchfiltertoggle_isOpen_binding,
    		func,
    		func_1,
    		click_handler,
    		click_handler_1,
    		searchsort_sortText_binding,
    		sort_handler,
    		searchfilters_isOpen_binding
    	];
    }

    class Search extends SvelteComponent {
    	constructor(options) {
    		super();

    		init(
    			this,
    			options,
    			instance$h,
    			create_fragment$h,
    			safe_not_equal,
    			{
    				refreshLiveRegion: 0,
    				filter: 17,
    				index: 18,
    				searchText: 16,
    				labels: 1,
    				onSearch: 2
    			},
    			null,
    			[-1, -1]
    		);
    	}

    	get filter() {
    		return this.$$.ctx[17];
    	}

    	get onSearch() {
    		return this.$$.ctx[2];
    	}
    }

    /* src/ProjectGrid.svelte generated by Svelte v3.48.0 */
    const get_bottom_slot_changes = dirty => ({ rows: dirty & /*visibleRows*/ 8 });
    const get_bottom_slot_context = ctx => ({ rows: /*visibleRows*/ ctx[3] });
    const get_foot_slot_changes = dirty => ({ rows: dirty & /*visibleRows*/ 8 });
    const get_foot_slot_context = ctx => ({ rows: /*visibleRows*/ ctx[3] });
    const get_default_slot_changes = dirty => ({ rows: dirty & /*visibleRows*/ 8 });
    const get_default_slot_context = ctx => ({ rows: /*visibleRows*/ ctx[3] });
    const get_left_slot_changes = dirty => ({ rows: dirty & /*visibleRows*/ 8 });
    const get_left_slot_context = ctx => ({ rows: /*visibleRows*/ ctx[3] });
    const get_head_slot_changes = dirty => ({ rows: dirty & /*visibleRows*/ 8 });
    const get_head_slot_context = ctx => ({ rows: /*visibleRows*/ ctx[3] });

    // (65:4) {:else}
    function create_else_block$3(ctx) {
    	let ul;
    	let ul_class_value;
    	let current;
    	const default_slot_template = /*#slots*/ ctx[12].default;
    	const default_slot = create_slot(default_slot_template, ctx, /*$$scope*/ ctx[11], get_default_slot_context);

    	return {
    		c() {
    			ul = element("ul");
    			if (default_slot) default_slot.c();

    			attr(ul, "class", ul_class_value = "pb-projects-" + (/*isDesktop*/ ctx[4]
    			? /*toggleView*/ ctx[1].toLowerCase()
    			: 'list'));

    			attr(ul, "aria-label", window.Drupal.t('Projects'));
    		},
    		m(target, anchor) {
    			insert(target, ul, anchor);

    			if (default_slot) {
    				default_slot.m(ul, null);
    			}

    			current = true;
    		},
    		p(ctx, dirty) {
    			if (default_slot) {
    				if (default_slot.p && (!current || dirty & /*$$scope, visibleRows*/ 2056)) {
    					update_slot_base(
    						default_slot,
    						default_slot_template,
    						ctx,
    						/*$$scope*/ ctx[11],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[11])
    						: get_slot_changes(default_slot_template, /*$$scope*/ ctx[11], dirty, get_default_slot_changes),
    						get_default_slot_context
    					);
    				}
    			}

    			if (!current || dirty & /*isDesktop, toggleView*/ 18 && ul_class_value !== (ul_class_value = "pb-projects-" + (/*isDesktop*/ ctx[4]
    			? /*toggleView*/ ctx[1].toLowerCase()
    			: 'list'))) {
    				attr(ul, "class", ul_class_value);
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(default_slot, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(default_slot, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(ul);
    			if (default_slot) default_slot.d(detaching);
    		}
    	};
    }

    // (63:39) 
    function create_if_block_1$9(ctx) {
    	let div;
    	let raw_value = /*labels*/ ctx[2].empty + "";

    	return {
    		c() {
    			div = element("div");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			div.innerHTML = raw_value;
    		},
    		p(ctx, dirty) {
    			if (dirty & /*labels*/ 4 && raw_value !== (raw_value = /*labels*/ ctx[2].empty + "")) div.innerHTML = raw_value;		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(div);
    		}
    	};
    }

    // (61:4) {#if loading}
    function create_if_block$a(ctx) {
    	let loading_1;
    	let current;
    	loading_1 = new Loading({});

    	return {
    		c() {
    			create_component(loading_1.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(loading_1, target, anchor);
    			current = true;
    		},
    		p: noop,
    		i(local) {
    			if (current) return;
    			transition_in(loading_1.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(loading_1.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(loading_1, detaching);
    		}
    	};
    }

    function create_fragment$g(ctx) {
    	let t0;
    	let div1;
    	let aside;
    	let t1;
    	let div0;
    	let current_block_type_index;
    	let if_block;
    	let t2;
    	let t3;
    	let current;
    	const head_slot_template = /*#slots*/ ctx[12].head;
    	const head_slot = create_slot(head_slot_template, ctx, /*$$scope*/ ctx[11], get_head_slot_context);
    	const left_slot_template = /*#slots*/ ctx[12].left;
    	const left_slot = create_slot(left_slot_template, ctx, /*$$scope*/ ctx[11], get_left_slot_context);
    	const if_block_creators = [create_if_block$a, create_if_block_1$9, create_else_block$3];
    	const if_blocks = [];

    	function select_block_type(ctx, dirty) {
    		if (/*loading*/ ctx[0]) return 0;
    		if (/*visibleRows*/ ctx[3].length === 0) return 1;
    		return 2;
    	}

    	current_block_type_index = select_block_type(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    	const foot_slot_template = /*#slots*/ ctx[12].foot;
    	const foot_slot = create_slot(foot_slot_template, ctx, /*$$scope*/ ctx[11], get_foot_slot_context);
    	const bottom_slot_template = /*#slots*/ ctx[12].bottom;
    	const bottom_slot = create_slot(bottom_slot_template, ctx, /*$$scope*/ ctx[11], get_bottom_slot_context);

    	return {
    		c() {
    			if (head_slot) head_slot.c();
    			t0 = space();
    			div1 = element("div");
    			aside = element("aside");
    			if (left_slot) left_slot.c();
    			t1 = space();
    			div0 = element("div");
    			if_block.c();
    			t2 = space();
    			if (foot_slot) foot_slot.c();
    			t3 = space();
    			if (bottom_slot) bottom_slot.c();
    			attr(aside, "class", "pb-layout__aside");
    			attr(div0, "class", "pb-layout__main");
    			attr(div1, "class", "pb-layout");
    		},
    		m(target, anchor) {
    			if (head_slot) {
    				head_slot.m(target, anchor);
    			}

    			insert(target, t0, anchor);
    			insert(target, div1, anchor);
    			append(div1, aside);

    			if (left_slot) {
    				left_slot.m(aside, null);
    			}

    			append(div1, t1);
    			append(div1, div0);
    			if_blocks[current_block_type_index].m(div0, null);
    			append(div0, t2);

    			if (foot_slot) {
    				foot_slot.m(div0, null);
    			}

    			insert(target, t3, anchor);

    			if (bottom_slot) {
    				bottom_slot.m(target, anchor);
    			}

    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if (head_slot) {
    				if (head_slot.p && (!current || dirty & /*$$scope, visibleRows*/ 2056)) {
    					update_slot_base(
    						head_slot,
    						head_slot_template,
    						ctx,
    						/*$$scope*/ ctx[11],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[11])
    						: get_slot_changes(head_slot_template, /*$$scope*/ ctx[11], dirty, get_head_slot_changes),
    						get_head_slot_context
    					);
    				}
    			}

    			if (left_slot) {
    				if (left_slot.p && (!current || dirty & /*$$scope, visibleRows*/ 2056)) {
    					update_slot_base(
    						left_slot,
    						left_slot_template,
    						ctx,
    						/*$$scope*/ ctx[11],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[11])
    						: get_slot_changes(left_slot_template, /*$$scope*/ ctx[11], dirty, get_left_slot_changes),
    						get_left_slot_context
    					);
    				}
    			}

    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if_blocks[current_block_type_index].p(ctx, dirty);
    			} else {
    				group_outros();

    				transition_out(if_blocks[previous_block_index], 1, 1, () => {
    					if_blocks[previous_block_index] = null;
    				});

    				check_outros();
    				if_block = if_blocks[current_block_type_index];

    				if (!if_block) {
    					if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    					if_block.c();
    				} else {
    					if_block.p(ctx, dirty);
    				}

    				transition_in(if_block, 1);
    				if_block.m(div0, t2);
    			}

    			if (foot_slot) {
    				if (foot_slot.p && (!current || dirty & /*$$scope, visibleRows*/ 2056)) {
    					update_slot_base(
    						foot_slot,
    						foot_slot_template,
    						ctx,
    						/*$$scope*/ ctx[11],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[11])
    						: get_slot_changes(foot_slot_template, /*$$scope*/ ctx[11], dirty, get_foot_slot_changes),
    						get_foot_slot_context
    					);
    				}
    			}

    			if (bottom_slot) {
    				if (bottom_slot.p && (!current || dirty & /*$$scope, visibleRows*/ 2056)) {
    					update_slot_base(
    						bottom_slot,
    						bottom_slot_template,
    						ctx,
    						/*$$scope*/ ctx[11],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[11])
    						: get_slot_changes(bottom_slot_template, /*$$scope*/ ctx[11], dirty, get_bottom_slot_changes),
    						get_bottom_slot_context
    					);
    				}
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(head_slot, local);
    			transition_in(left_slot, local);
    			transition_in(if_block);
    			transition_in(foot_slot, local);
    			transition_in(bottom_slot, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(head_slot, local);
    			transition_out(left_slot, local);
    			transition_out(if_block);
    			transition_out(foot_slot, local);
    			transition_out(bottom_slot, local);
    			current = false;
    		},
    		d(detaching) {
    			if (head_slot) head_slot.d(detaching);
    			if (detaching) detach(t0);
    			if (detaching) detach(div1);
    			if (left_slot) left_slot.d(detaching);
    			if_blocks[current_block_type_index].d();
    			if (foot_slot) foot_slot.d(detaching);
    			if (detaching) detach(t3);
    			if (bottom_slot) bottom_slot.d(detaching);
    		}
    	};
    }

    function instance$g($$self, $$props, $$invalidate) {
    	let isDesktop;
    	let filteredRows;
    	let visibleRows;
    	let $pageSize;
    	component_subscribe($$self, pageSize, $$value => $$invalidate(10, $pageSize = $$value));
    	let { $$slots: slots = {}, $$scope } = $$props;
    	let { loading = false } = $$props;
    	let { page = 0 } = $$props;
    	let { pageIndex = 0 } = $$props;
    	let { toggleView } = $$props;
    	let { rows } = $$props;

    	let { labels = {
    		empty: window.Drupal.t('No modules found'),
    		loading: window.Drupal.t('Loading data')
    	} } = $$props;

    	let mqMatches;

    	mediaQueryValues.subscribe(mqlMap => {
    		$$invalidate(8, mqMatches = mqlMap.get('(min-width: 1200px)'));
    	});

    	setContext('state', {
    		getState: () => ({
    			page,
    			pageIndex,
    			pageSize,
    			rows,
    			filteredRows
    		}),
    		setPage: (_page, _pageIndex) => {
    			$$invalidate(6, page = _page);
    			$$invalidate(5, pageIndex = _pageIndex);
    		},
    		setRows: _rows => {
    			$$invalidate(9, filteredRows = _rows);
    		}
    	});

    	$$self.$$set = $$props => {
    		if ('loading' in $$props) $$invalidate(0, loading = $$props.loading);
    		if ('page' in $$props) $$invalidate(6, page = $$props.page);
    		if ('pageIndex' in $$props) $$invalidate(5, pageIndex = $$props.pageIndex);
    		if ('toggleView' in $$props) $$invalidate(1, toggleView = $$props.toggleView);
    		if ('rows' in $$props) $$invalidate(7, rows = $$props.rows);
    		if ('labels' in $$props) $$invalidate(2, labels = $$props.labels);
    		if ('$$scope' in $$props) $$invalidate(11, $$scope = $$props.$$scope);
    	};

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*mqMatches*/ 256) {
    			$$invalidate(4, isDesktop = mqMatches);
    		}

    		if ($$self.$$.dirty & /*rows*/ 128) {
    			$$invalidate(9, filteredRows = rows);
    		}

    		if ($$self.$$.dirty & /*filteredRows, pageIndex, $pageSize*/ 1568) {
    			$$invalidate(3, visibleRows = filteredRows
    			? filteredRows.slice(pageIndex, pageIndex + $pageSize)
    			: []);
    		}
    	};

    	return [
    		loading,
    		toggleView,
    		labels,
    		visibleRows,
    		isDesktop,
    		pageIndex,
    		page,
    		rows,
    		mqMatches,
    		filteredRows,
    		$pageSize,
    		$$scope,
    		slots
    	];
    }

    class ProjectGrid extends SvelteComponent {
    	constructor(options) {
    		super();

    		init(this, options, instance$g, create_fragment$g, safe_not_equal, {
    			loading: 0,
    			page: 6,
    			pageIndex: 5,
    			toggleView: 1,
    			rows: 7,
    			labels: 2
    		});
    	}
    }

    /* src/PagerItem.svelte generated by Svelte v3.48.0 */

    function create_fragment$f(ctx) {
    	let li;
    	let a;
    	let t;
    	let a_class_value;
    	let a_aria_label_value;
    	let a_aria_current_value;
    	let li_class_value;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			li = element("li");
    			a = element("a");
    			t = text(/*label*/ ctx[2]);
    			attr(a, "href", '#');
    			attr(a, "class", a_class_value = `pager__link ${/*linkTypes*/ ctx[1].map(func).join(' ')}`);
    			attr(a, "aria-label", a_aria_label_value = /*ariaLabel*/ ctx[4] || window.Drupal.t('@location page', { '@location': /*label*/ ctx[2] }));
    			attr(a, "aria-current", a_aria_current_value = /*isCurrent*/ ctx[5] ? 'page' : null);
    			toggle_class(a, "is-active", /*isCurrent*/ ctx[5]);
    			attr(li, "class", li_class_value = `pager__item ${/*itemTypes*/ ctx[0].map(func_1).join(' ')}`);
    			toggle_class(li, "pager__item--active", /*isCurrent*/ ctx[5]);
    		},
    		m(target, anchor) {
    			insert(target, li, anchor);
    			append(li, a);
    			append(a, t);

    			if (!mounted) {
    				dispose = listen(a, "click", /*click_handler*/ ctx[7]);
    				mounted = true;
    			}
    		},
    		p(ctx, [dirty]) {
    			if (dirty & /*label*/ 4) set_data(t, /*label*/ ctx[2]);

    			if (dirty & /*linkTypes*/ 2 && a_class_value !== (a_class_value = `pager__link ${/*linkTypes*/ ctx[1].map(func).join(' ')}`)) {
    				attr(a, "class", a_class_value);
    			}

    			if (dirty & /*ariaLabel, label*/ 20 && a_aria_label_value !== (a_aria_label_value = /*ariaLabel*/ ctx[4] || window.Drupal.t('@location page', { '@location': /*label*/ ctx[2] }))) {
    				attr(a, "aria-label", a_aria_label_value);
    			}

    			if (dirty & /*isCurrent*/ 32 && a_aria_current_value !== (a_aria_current_value = /*isCurrent*/ ctx[5] ? 'page' : null)) {
    				attr(a, "aria-current", a_aria_current_value);
    			}

    			if (dirty & /*linkTypes, isCurrent*/ 34) {
    				toggle_class(a, "is-active", /*isCurrent*/ ctx[5]);
    			}

    			if (dirty & /*itemTypes*/ 1 && li_class_value !== (li_class_value = `pager__item ${/*itemTypes*/ ctx[0].map(func_1).join(' ')}`)) {
    				attr(li, "class", li_class_value);
    			}

    			if (dirty & /*itemTypes, isCurrent*/ 33) {
    				toggle_class(li, "pager__item--active", /*isCurrent*/ ctx[5]);
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(li);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    const func = item => `pager__link--${item}`;
    const func_1 = item => `pager__item--${item}`;

    function instance$f($$self, $$props, $$invalidate) {
    	const dispatch = createEventDispatcher();
    	const stateContext = getContext('state');
    	let { itemTypes = [] } = $$props;
    	let { linkTypes = [] } = $$props;
    	let { label = '' } = $$props;
    	let { toPage = 0 } = $$props;
    	let { ariaLabel = null } = $$props;
    	let { isCurrent = false } = $$props;

    	function onChange(event, selectedPage) {
    		const state = stateContext.getState();

    		const detail = {
    			originalEvent: event,
    			page: selectedPage,
    			pageIndex: 0,
    			pageSize: state.pageSize
    		};

    		dispatch('pageChange', detail);

    		if (detail.preventDefault !== true) {
    			stateContext.setPage(detail.page, detail.pageIndex);
    		}
    	}

    	const click_handler = e => onChange(e, toPage);

    	$$self.$$set = $$props => {
    		if ('itemTypes' in $$props) $$invalidate(0, itemTypes = $$props.itemTypes);
    		if ('linkTypes' in $$props) $$invalidate(1, linkTypes = $$props.linkTypes);
    		if ('label' in $$props) $$invalidate(2, label = $$props.label);
    		if ('toPage' in $$props) $$invalidate(3, toPage = $$props.toPage);
    		if ('ariaLabel' in $$props) $$invalidate(4, ariaLabel = $$props.ariaLabel);
    		if ('isCurrent' in $$props) $$invalidate(5, isCurrent = $$props.isCurrent);
    	};

    	return [
    		itemTypes,
    		linkTypes,
    		label,
    		toPage,
    		ariaLabel,
    		isCurrent,
    		onChange,
    		click_handler
    	];
    }

    class PagerItem extends SvelteComponent {
    	constructor(options) {
    		super();

    		init(this, options, instance$f, create_fragment$f, safe_not_equal, {
    			itemTypes: 0,
    			linkTypes: 1,
    			label: 2,
    			toPage: 3,
    			ariaLabel: 4,
    			isCurrent: 5
    		});
    	}
    }

    /* src/Pagination.svelte generated by Svelte v3.48.0 */

    function get_each_context$5(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[17] = list[i];
    	return child_ctx;
    }

    function get_each_context_1$1(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[20] = list[i];
    	return child_ctx;
    }

    // (35:0) {#if pageCount > 0}
    function create_if_block$9(ctx) {
    	let nav;
    	let label;
    	let t1;
    	let select;
    	let t2;
    	let ul;
    	let t3;
    	let t4;
    	let t5;
    	let t6;
    	let nav_aria_label_value;
    	let current;
    	let mounted;
    	let dispose;
    	let each_value_1 = /*options*/ ctx[6];
    	let each_blocks_1 = [];

    	for (let i = 0; i < each_value_1.length; i += 1) {
    		each_blocks_1[i] = create_each_block_1$1(get_each_context_1$1(ctx, each_value_1, i));
    	}

    	let if_block0 = /*page*/ ctx[1] !== 0 && create_if_block_5$2(ctx);
    	let if_block1 = /*page*/ ctx[1] >= 5 && create_if_block_4$3();
    	let each_value = /*buttons*/ ctx[0];
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$5(get_each_context$5(ctx, each_value, i));
    	}

    	const out = i => transition_out(each_blocks[i], 1, 1, () => {
    		each_blocks[i] = null;
    	});

    	let if_block2 = /*page*/ ctx[1] + 5 <= /*pageCount*/ ctx[4] && create_if_block_2$5();
    	let if_block3 = /*page*/ ctx[1] !== /*pageCount*/ ctx[4] && create_if_block_1$8(ctx);

    	return {
    		c() {
    			nav = element("nav");
    			label = element("label");
    			label.textContent = `${window.Drupal.t('Modules per page')}`;
    			t1 = space();
    			select = element("select");

    			for (let i = 0; i < each_blocks_1.length; i += 1) {
    				each_blocks_1[i].c();
    			}

    			t2 = space();
    			ul = element("ul");
    			if (if_block0) if_block0.c();
    			t3 = space();
    			if (if_block1) if_block1.c();
    			t4 = space();

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t5 = space();
    			if (if_block2) if_block2.c();
    			t6 = space();
    			if (if_block3) if_block3.c();
    			attr(label, "for", "num-projects");
    			attr(select, "class", "pagination__num-projects");
    			attr(select, "id", "num-projects");
    			attr(select, "name", "num-projects");
    			if (/*$pageSize*/ ctx[3] === void 0) add_render_callback(() => /*select_change_handler*/ ctx[8].call(select));
    			attr(ul, "class", "pagination__pager-items pager__items js-pager__items");
    			attr(nav, "class", "pager pagination__pager");
    			attr(nav, "aria-label", nav_aria_label_value = window.Drupal.t('Project Browser Pagination'));
    			attr(nav, "role", "navigation");
    		},
    		m(target, anchor) {
    			insert(target, nav, anchor);
    			append(nav, label);
    			append(nav, t1);
    			append(nav, select);

    			for (let i = 0; i < each_blocks_1.length; i += 1) {
    				each_blocks_1[i].m(select, null);
    			}

    			select_option(select, /*$pageSize*/ ctx[3]);
    			append(nav, t2);
    			append(nav, ul);
    			if (if_block0) if_block0.m(ul, null);
    			append(ul, t3);
    			if (if_block1) if_block1.m(ul, null);
    			append(ul, t4);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(ul, null);
    			}

    			append(ul, t5);
    			if (if_block2) if_block2.m(ul, null);
    			append(ul, t6);
    			if (if_block3) if_block3.m(ul, null);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen(select, "change", /*select_change_handler*/ ctx[8]),
    					listen(select, "change", /*change_handler*/ ctx[9])
    				];

    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			if (dirty & /*options*/ 64) {
    				each_value_1 = /*options*/ ctx[6];
    				let i;

    				for (i = 0; i < each_value_1.length; i += 1) {
    					const child_ctx = get_each_context_1$1(ctx, each_value_1, i);

    					if (each_blocks_1[i]) {
    						each_blocks_1[i].p(child_ctx, dirty);
    					} else {
    						each_blocks_1[i] = create_each_block_1$1(child_ctx);
    						each_blocks_1[i].c();
    						each_blocks_1[i].m(select, null);
    					}
    				}

    				for (; i < each_blocks_1.length; i += 1) {
    					each_blocks_1[i].d(1);
    				}

    				each_blocks_1.length = each_value_1.length;
    			}

    			if (dirty & /*$pageSize, options*/ 72) {
    				select_option(select, /*$pageSize*/ ctx[3]);
    			}

    			if (/*page*/ ctx[1] !== 0) {
    				if (if_block0) {
    					if_block0.p(ctx, dirty);

    					if (dirty & /*page*/ 2) {
    						transition_in(if_block0, 1);
    					}
    				} else {
    					if_block0 = create_if_block_5$2(ctx);
    					if_block0.c();
    					transition_in(if_block0, 1);
    					if_block0.m(ul, t3);
    				}
    			} else if (if_block0) {
    				group_outros();

    				transition_out(if_block0, 1, 1, () => {
    					if_block0 = null;
    				});

    				check_outros();
    			}

    			if (/*page*/ ctx[1] >= 5) {
    				if (if_block1) ; else {
    					if_block1 = create_if_block_4$3();
    					if_block1.c();
    					if_block1.m(ul, t4);
    				}
    			} else if (if_block1) {
    				if_block1.d(1);
    				if_block1 = null;
    			}

    			if (dirty & /*buttons, page, window, pageCount*/ 19) {
    				each_value = /*buttons*/ ctx[0];
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$5(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    						transition_in(each_blocks[i], 1);
    					} else {
    						each_blocks[i] = create_each_block$5(child_ctx);
    						each_blocks[i].c();
    						transition_in(each_blocks[i], 1);
    						each_blocks[i].m(ul, t5);
    					}
    				}

    				group_outros();

    				for (i = each_value.length; i < each_blocks.length; i += 1) {
    					out(i);
    				}

    				check_outros();
    			}

    			if (/*page*/ ctx[1] + 5 <= /*pageCount*/ ctx[4]) {
    				if (if_block2) ; else {
    					if_block2 = create_if_block_2$5();
    					if_block2.c();
    					if_block2.m(ul, t6);
    				}
    			} else if (if_block2) {
    				if_block2.d(1);
    				if_block2 = null;
    			}

    			if (/*page*/ ctx[1] !== /*pageCount*/ ctx[4]) {
    				if (if_block3) {
    					if_block3.p(ctx, dirty);

    					if (dirty & /*page, pageCount*/ 18) {
    						transition_in(if_block3, 1);
    					}
    				} else {
    					if_block3 = create_if_block_1$8(ctx);
    					if_block3.c();
    					transition_in(if_block3, 1);
    					if_block3.m(ul, null);
    				}
    			} else if (if_block3) {
    				group_outros();

    				transition_out(if_block3, 1, 1, () => {
    					if_block3 = null;
    				});

    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block0);

    			for (let i = 0; i < each_value.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			transition_in(if_block3);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block0);
    			each_blocks = each_blocks.filter(Boolean);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			transition_out(if_block3);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(nav);
    			destroy_each(each_blocks_1, detaching);
    			if (if_block0) if_block0.d();
    			if (if_block1) if_block1.d();
    			destroy_each(each_blocks, detaching);
    			if (if_block2) if_block2.d();
    			if (if_block3) if_block3.d();
    			mounted = false;
    			run_all(dispose);
    		}
    	};
    }

    // (53:6) {#each options as option}
    function create_each_block_1$1(ctx) {
    	let option;
    	let t_value = /*option*/ ctx[20].value + "";
    	let t;
    	let option_value_value;

    	return {
    		c() {
    			option = element("option");
    			t = text(t_value);
    			option.__value = option_value_value = /*option*/ ctx[20].id;
    			option.value = option.__value;
    		},
    		m(target, anchor) {
    			insert(target, option, anchor);
    			append(option, t);
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(option);
    		}
    	};
    }

    // (58:6) {#if page !== 0}
    function create_if_block_5$2(ctx) {
    	let pageritem0;
    	let t;
    	let pageritem1;
    	let current;

    	pageritem0 = new PagerItem({
    			props: {
    				itemTypes: ['action', 'first'],
    				linkTypes: ['action-link', 'backward'],
    				label: /*labels*/ ctx[2].first,
    				toPage: 0
    			}
    		});

    	pageritem0.$on("pageChange", /*pageChange_handler*/ ctx[10]);

    	pageritem1 = new PagerItem({
    			props: {
    				itemTypes: ['action', 'previous'],
    				linkTypes: ['action-link', 'backward'],
    				label: /*labels*/ ctx[2].previous,
    				toPage: /*page*/ ctx[1] - 1
    			}
    		});

    	pageritem1.$on("pageChange", /*pageChange_handler_1*/ ctx[11]);

    	return {
    		c() {
    			create_component(pageritem0.$$.fragment);
    			t = space();
    			create_component(pageritem1.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(pageritem0, target, anchor);
    			insert(target, t, anchor);
    			mount_component(pageritem1, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const pageritem0_changes = {};
    			if (dirty & /*labels*/ 4) pageritem0_changes.label = /*labels*/ ctx[2].first;
    			pageritem0.$set(pageritem0_changes);
    			const pageritem1_changes = {};
    			if (dirty & /*labels*/ 4) pageritem1_changes.label = /*labels*/ ctx[2].previous;
    			if (dirty & /*page*/ 2) pageritem1_changes.toPage = /*page*/ ctx[1] - 1;
    			pageritem1.$set(pageritem1_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(pageritem0.$$.fragment, local);
    			transition_in(pageritem1.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(pageritem0.$$.fragment, local);
    			transition_out(pageritem1.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(pageritem0, detaching);
    			if (detaching) detach(t);
    			destroy_component(pageritem1, detaching);
    		}
    	};
    }

    // (74:6) {#if page >= 5}
    function create_if_block_4$3(ctx) {
    	let li;

    	return {
    		c() {
    			li = element("li");
    			li.textContent = "…";
    			attr(li, "class", "pager__item pager__item--ellipsis");
    			attr(li, "role", "presentation");
    		},
    		m(target, anchor) {
    			insert(target, li, anchor);
    		},
    		d(detaching) {
    			if (detaching) detach(li);
    		}
    	};
    }

    // (80:8) {#if page + button >= 0 && page + button <= pageCount}
    function create_if_block_3$3(ctx) {
    	let pageritem;
    	let current;

    	pageritem = new PagerItem({
    			props: {
    				itemTypes: ['number'],
    				isCurrent: /*button*/ ctx[17] === 0 ? 'page' : null,
    				label: /*page*/ ctx[1] + /*button*/ ctx[17] + 1,
    				toPage: /*page*/ ctx[1] + /*button*/ ctx[17],
    				ariaLabel: window.Drupal.t('Page @page_number', {
    					'@page_number': /*page*/ ctx[1] + /*button*/ ctx[17] + 1
    				})
    			}
    		});

    	pageritem.$on("pageChange", /*pageChange_handler_2*/ ctx[12]);

    	return {
    		c() {
    			create_component(pageritem.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(pageritem, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const pageritem_changes = {};
    			if (dirty & /*buttons*/ 1) pageritem_changes.isCurrent = /*button*/ ctx[17] === 0 ? 'page' : null;
    			if (dirty & /*page, buttons*/ 3) pageritem_changes.label = /*page*/ ctx[1] + /*button*/ ctx[17] + 1;
    			if (dirty & /*page, buttons*/ 3) pageritem_changes.toPage = /*page*/ ctx[1] + /*button*/ ctx[17];

    			if (dirty & /*page, buttons*/ 3) pageritem_changes.ariaLabel = window.Drupal.t('Page @page_number', {
    				'@page_number': /*page*/ ctx[1] + /*button*/ ctx[17] + 1
    			});

    			pageritem.$set(pageritem_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(pageritem.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(pageritem.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(pageritem, detaching);
    		}
    	};
    }

    // (79:6) {#each buttons as button}
    function create_each_block$5(ctx) {
    	let if_block_anchor;
    	let current;
    	let if_block = /*page*/ ctx[1] + /*button*/ ctx[17] >= 0 && /*page*/ ctx[1] + /*button*/ ctx[17] <= /*pageCount*/ ctx[4] && create_if_block_3$3(ctx);

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if (/*page*/ ctx[1] + /*button*/ ctx[17] >= 0 && /*page*/ ctx[1] + /*button*/ ctx[17] <= /*pageCount*/ ctx[4]) {
    				if (if_block) {
    					if_block.p(ctx, dirty);

    					if (dirty & /*page, buttons, pageCount*/ 19) {
    						transition_in(if_block, 1);
    					}
    				} else {
    					if_block = create_if_block_3$3(ctx);
    					if_block.c();
    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				group_outros();

    				transition_out(if_block, 1, 1, () => {
    					if_block = null;
    				});

    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (93:6) {#if page + 5 <= pageCount}
    function create_if_block_2$5(ctx) {
    	let li;

    	return {
    		c() {
    			li = element("li");
    			li.textContent = "…";
    			attr(li, "class", "pager__item pager__item--ellipsis");
    			attr(li, "role", "presentation");
    		},
    		m(target, anchor) {
    			insert(target, li, anchor);
    		},
    		d(detaching) {
    			if (detaching) detach(li);
    		}
    	};
    }

    // (98:6) {#if page !== pageCount}
    function create_if_block_1$8(ctx) {
    	let pageritem0;
    	let t;
    	let pageritem1;
    	let current;

    	pageritem0 = new PagerItem({
    			props: {
    				itemTypes: ['action', 'next'],
    				linkTypes: ['action-link', 'forward'],
    				label: /*labels*/ ctx[2].next,
    				toPage: /*page*/ ctx[1] + 1
    			}
    		});

    	pageritem0.$on("pageChange", /*pageChange_handler_3*/ ctx[13]);

    	pageritem1 = new PagerItem({
    			props: {
    				itemTypes: ['action', 'last'],
    				linkTypes: ['action-link', 'forward'],
    				label: /*labels*/ ctx[2].last,
    				toPage: /*pageCount*/ ctx[4]
    			}
    		});

    	pageritem1.$on("pageChange", /*pageChange_handler_4*/ ctx[14]);

    	return {
    		c() {
    			create_component(pageritem0.$$.fragment);
    			t = space();
    			create_component(pageritem1.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(pageritem0, target, anchor);
    			insert(target, t, anchor);
    			mount_component(pageritem1, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const pageritem0_changes = {};
    			if (dirty & /*labels*/ 4) pageritem0_changes.label = /*labels*/ ctx[2].next;
    			if (dirty & /*page*/ 2) pageritem0_changes.toPage = /*page*/ ctx[1] + 1;
    			pageritem0.$set(pageritem0_changes);
    			const pageritem1_changes = {};
    			if (dirty & /*labels*/ 4) pageritem1_changes.label = /*labels*/ ctx[2].last;
    			if (dirty & /*pageCount*/ 16) pageritem1_changes.toPage = /*pageCount*/ ctx[4];
    			pageritem1.$set(pageritem1_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(pageritem0.$$.fragment, local);
    			transition_in(pageritem1.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(pageritem0.$$.fragment, local);
    			transition_out(pageritem1.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(pageritem0, detaching);
    			if (detaching) detach(t);
    			destroy_component(pageritem1, detaching);
    		}
    	};
    }

    function create_fragment$e(ctx) {
    	let if_block_anchor;
    	let current;
    	let if_block = /*pageCount*/ ctx[4] > 0 && create_if_block$9(ctx);

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if (/*pageCount*/ ctx[4] > 0) {
    				if (if_block) {
    					if_block.p(ctx, dirty);

    					if (dirty & /*pageCount*/ 16) {
    						transition_in(if_block, 1);
    					}
    				} else {
    					if_block = create_if_block$9(ctx);
    					if_block.c();
    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				group_outros();

    				transition_out(if_block, 1, 1, () => {
    					if_block = null;
    				});

    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    function instance$e($$self, $$props, $$invalidate) {
    	let pageCount;
    	let $pageSize;
    	component_subscribe($$self, pageSize, $$value => $$invalidate(3, $pageSize = $$value));
    	const dispatch = createEventDispatcher();

    	function pageSizeChange() {
    		dispatch('pageSizeChange');
    	}
    	let { buttons = [-4, -3, -2, -1, 0, 1, 2, 3, 4] } = $$props;
    	let { count } = $$props;
    	let { page = 0 } = $$props;

    	let { labels = {
    		first: window.Drupal.t('First'),
    		last: window.Drupal.t('Last'),
    		next: window.Drupal.t('Next'),
    		previous: window.Drupal.t('Previous')
    	} } = $$props;

    	const options = [
    		{ id: 12, value: 12 },
    		{ id: 24, value: 24 },
    		{ id: 36, value: 36 },
    		{ id: 48, value: 48 }
    	];

    	function select_change_handler() {
    		$pageSize = select_value(this);
    		pageSize.set($pageSize);
    		$$invalidate(6, options);
    	}

    	const change_handler = () => {
    		pageSizeChange();
    	};

    	function pageChange_handler(event) {
    		bubble.call(this, $$self, event);
    	}

    	function pageChange_handler_1(event) {
    		bubble.call(this, $$self, event);
    	}

    	function pageChange_handler_2(event) {
    		bubble.call(this, $$self, event);
    	}

    	function pageChange_handler_3(event) {
    		bubble.call(this, $$self, event);
    	}

    	function pageChange_handler_4(event) {
    		bubble.call(this, $$self, event);
    	}

    	$$self.$$set = $$props => {
    		if ('buttons' in $$props) $$invalidate(0, buttons = $$props.buttons);
    		if ('count' in $$props) $$invalidate(7, count = $$props.count);
    		if ('page' in $$props) $$invalidate(1, page = $$props.page);
    		if ('labels' in $$props) $$invalidate(2, labels = $$props.labels);
    	};

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*count, $pageSize*/ 136) {
    			$$invalidate(4, pageCount = Math.ceil(count / $pageSize) - 1);
    		}
    	};

    	return [
    		buttons,
    		page,
    		labels,
    		$pageSize,
    		pageCount,
    		pageSizeChange,
    		options,
    		count,
    		select_change_handler,
    		change_handler,
    		pageChange_handler,
    		pageChange_handler_1,
    		pageChange_handler_2,
    		pageChange_handler_3,
    		pageChange_handler_4
    	];
    }

    class Pagination extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$e, create_fragment$e, safe_not_equal, { buttons: 0, count: 7, page: 1, labels: 2 });
    	}
    }

    // cspell:ignore dont
    const { once, Drupal } = window;

    /**
     * Finds [data-copy-command] buttons and adds copy functionality to them.
     */
    const enableCopyButtons = () => {
      setTimeout(() => {
        once('copyButton', '[data-copy-command]').forEach((copyButton) => {
          copyButton.addEventListener('click', (e) => {
            // The copy button must be contained in a div
            const container = e.target.closest('div');
            // The only <input> within the parent dive should have its value set
            // to the command that should be copied.
            const input = container.querySelector('input');

            // Make the input value the selected text
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value);
            Drupal.announce(Drupal.t('Copied text to clipboard'));

            // Create a "receipt" that will visually show the text has been copied.
            const receipt = document.createElement('div');
            receipt.textContent = Drupal.t('Copied');
            receipt.classList.add('copied-action');
            receipt.style.opacity = '1';
            input.insertAdjacentElement('afterend', receipt);
            // eslint-disable-next-line max-nested-callbacks
            setTimeout(() => {
              // Remove the receipt after 1 second.
              receipt.remove();
            }, 1000);
          });
        });
      });
    };

    const getCommandsPopupMessage = (project) => {
      // @todo move the message provided in this condition to the 'commands'
      // property of the project definition.
      if (project.type === 'module:drupalorg') {
        const download = Drupal.t('Download');
        const composerText = Drupal.t(
          'The !use_composer_open recommended way!close to download any Drupal module is with !get_composer_open Composer!close.',
          {
            '!close': '</a>',
            '!use_composer_open':
              '<a href="https://www.drupal.org/docs/develop/using-composer/using-composer-to-install-drupal-and-manage-dependencies#managing-contributed" target="_blank" rel="noreferrer noopener">',
            '!get_composer_open':
              '<a href="https://getcomposer.org/" target="_blank" rel="noopener noreferrer">',
          },
        );
        const composerExistsText = Drupal.t(
          "If you already manage your Drupal application dependencies with Composer, run the following from the command line in your application's Composer root directory",
        );
        const infoText = Drupal.t('This will download the module to your codebase.');
        const composerDontWorkText = Drupal.t(
          "Didn't work? !learn_open Learn how to troubleshoot Composer!close",
          {
            '!learn_open':
              '<a href="https://getcomposer.org/doc/articles/troubleshooting.md" target="_blank" rel="noopener noreferrer">',
            '!close': '</a>',
          },
        );
        const downloadModuleText = Drupal.t(
          'If you cannot use Composer, you may !dl_manually_open download the module manually through your browser!close',
          {
            '!dl_manually_open':
              '<a href="https://www.drupal.org/docs/user_guide/en/extend-module-install.html#s-using-the-administrative-interface" target="_blank" rel="noreferrer">',
            '!close': '</a>',
          },
        );
        const install = Drupal.t('Install');
        const installText = Drupal.t(
          'Go to the !module_page_open Extend page!close (admin/modules), check the box next to each module you wish to enable, then click the Install button at the bottom of the page.',
          {
            '!module_page_open': `<a href="${ORIGIN_URL}/admin/modules" target="_blank" rel="noopener noreferrer">`,
            '!close': '</a>',
          },
        );
        const drushText = Drupal.t(
          'Alternatively, you can use !drush_openDrush!close to install it via the command line',
          {
            '!drush_open': '<a href="https://www.drush.org/latest/" target="_blank" rel="noopener noreferrer">',
            '!close': '</a>',
          },
        );
        const installDrush = Drupal.t(
          'If Drush is not installed, this will add the tool to your codebase',
        );
        const downloadAlt = Drupal.t('Copy the download command');
        const installAlt = Drupal.t('Copy the install command');
        const drushAlt = Drupal.t('Copy the install Drush command');
        const copyIcon = `${FULL_MODULE_PATH}/images/copy-icon.svg`;
        const makeButton = (altText, action) => `<button data-copy-command id="${action}-btn"><img src="${copyIcon}" alt="${altText}"/></button>`;
        const downloadCopyButton =  makeButton(downloadAlt, 'download');
        const installCopyButton = makeButton(installAlt, 'install');
        const installDrushCopyButton = makeButton(drushAlt, 'install-drush');

        const div = document.createElement('div');
        div.classList.add('window');
        div.innerHTML = `<h3>1. ${download}</h3>
              <p>${composerText}</p>
              <p>${composerExistsText}:</p>
              <div class="command-box">
                <input value="composer require ${project.package_name}" readonly/>
                ${downloadCopyButton}
              </div>

              <p>${infoText}</p>
              <p>${composerDontWorkText}.</p>
              <p>${downloadModuleText}.</p>
              <h3>2. ${install}</h3>
              <p>${installText}</p>
              <p>${drushText}:</p>
              <div class="command-box">
                <input value="drush pm:install ${project.project_machine_name}" readonly/>
                ${installCopyButton}
              </div>
              </div>

              <p>${installDrush}:</p>
              <div class="command-box">
                <input value="composer require drush/drush" readonly/>
                ${installDrushCopyButton}
              </div>
              <style>
                .action-link {
                  margin: 0 2px;
                  padding: 0.25rem 0.25rem;
                  border: 1px solid;
                }
              </style>`;
        enableCopyButtons();
        return div;
      }
      if (project.commands) {
        const div = document.createElement('div');
        div.innerHTML = project.commands;
        enableCopyButtons();
        return div;
      }

    };

    const openPopup = (getMessage, project) => {
      const message = typeof getMessage === 'function' ? getMessage() : getMessage;
      const popupModal = Drupal.dialog(message, {
        title: project.title,
        classes: {'ui-dialog': 'project-browser-popup'},
        width: '50rem',
      });
      popupModal.showModal();
    };

    /* src/Project/ProjectButtonBase.svelte generated by Svelte v3.48.0 */

    function create_fragment$d(ctx) {
    	let button;
    	let current;
    	let mounted;
    	let dispose;
    	const default_slot_template = /*#slots*/ ctx[3].default;
    	const default_slot = create_slot(default_slot_template, ctx, /*$$scope*/ ctx[2], null);
    	let button_levels = [{ class: "project__action_button" }, /*$$restProps*/ ctx[1]];
    	let button_data = {};

    	for (let i = 0; i < button_levels.length; i += 1) {
    		button_data = assign(button_data, button_levels[i]);
    	}

    	return {
    		c() {
    			button = element("button");
    			if (default_slot) default_slot.c();
    			set_attributes(button, button_data);
    		},
    		m(target, anchor) {
    			insert(target, button, anchor);

    			if (default_slot) {
    				default_slot.m(button, null);
    			}

    			if (button.autofocus) button.focus();
    			current = true;

    			if (!mounted) {
    				dispose = listen(button, "click", function () {
    					if (is_function(/*click*/ ctx[0])) /*click*/ ctx[0].apply(this, arguments);
    				});

    				mounted = true;
    			}
    		},
    		p(new_ctx, [dirty]) {
    			ctx = new_ctx;

    			if (default_slot) {
    				if (default_slot.p && (!current || dirty & /*$$scope*/ 4)) {
    					update_slot_base(
    						default_slot,
    						default_slot_template,
    						ctx,
    						/*$$scope*/ ctx[2],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[2])
    						: get_slot_changes(default_slot_template, /*$$scope*/ ctx[2], dirty, null),
    						null
    					);
    				}
    			}

    			set_attributes(button, button_data = get_spread_update(button_levels, [
    				{ class: "project__action_button" },
    				dirty & /*$$restProps*/ 2 && /*$$restProps*/ ctx[1]
    			]));
    		},
    		i(local) {
    			if (current) return;
    			transition_in(default_slot, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(default_slot, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(button);
    			if (default_slot) default_slot.d(detaching);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    function instance$d($$self, $$props, $$invalidate) {
    	const omit_props_names = ["click"];
    	let $$restProps = compute_rest_props($$props, omit_props_names);
    	let { $$slots: slots = {}, $$scope } = $$props;

    	let { click = () => {
    		
    	} } = $$props;

    	$$self.$$set = $$new_props => {
    		$$props = assign(assign({}, $$props), exclude_internal_props($$new_props));
    		$$invalidate(1, $$restProps = compute_rest_props($$props, omit_props_names));
    		if ('click' in $$new_props) $$invalidate(0, click = $$new_props.click);
    		if ('$$scope' in $$new_props) $$invalidate(2, $$scope = $$new_props.$$scope);
    	};

    	return [click, $$restProps, $$scope, slots];
    }

    class ProjectButtonBase extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$d, create_fragment$d, safe_not_equal, { click: 0 });
    	}
    }

    /* src/Project/AddInstallButton.svelte generated by Svelte v3.48.0 */

    function create_default_slot$2(ctx) {
    	let t0_value = (/*alreadyAdded*/ ctx[1]
    	? window.Drupal.t('Install')
    	: window.Drupal.t('Add and Install')) + "";

    	let t0;
    	let span;
    	let t1_value = /*project*/ ctx[0].title + "";
    	let t1;

    	return {
    		c() {
    			t0 = text(t0_value);
    			span = element("span");
    			t1 = text(t1_value);
    			attr(span, "class", "visually-hidden");
    		},
    		m(target, anchor) {
    			insert(target, t0, anchor);
    			insert(target, span, anchor);
    			append(span, t1);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*alreadyAdded*/ 2 && t0_value !== (t0_value = (/*alreadyAdded*/ ctx[1]
    			? window.Drupal.t('Install')
    			: window.Drupal.t('Add and Install')) + "")) set_data(t0, t0_value);

    			if (dirty & /*project*/ 1 && t1_value !== (t1_value = /*project*/ ctx[0].title + "")) set_data(t1, t1_value);
    		},
    		d(detaching) {
    			if (detaching) detach(t0);
    			if (detaching) detach(span);
    		}
    	};
    }

    function create_fragment$c(ctx) {
    	let projectbuttonbase;
    	let current;

    	projectbuttonbase = new ProjectButtonBase({
    			props: {
    				click: /*func*/ ctx[9],
    				disabled: PM_VALIDATION_ERROR && /*$isPackageManagerRequired*/ ctx[2],
    				$$slots: { default: [create_default_slot$2] },
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			create_component(projectbuttonbase.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(projectbuttonbase, target, anchor);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			const projectbuttonbase_changes = {};
    			if (dirty & /*alreadyAdded*/ 2) projectbuttonbase_changes.click = /*func*/ ctx[9];
    			if (dirty & /*$isPackageManagerRequired*/ 4) projectbuttonbase_changes.disabled = PM_VALIDATION_ERROR && /*$isPackageManagerRequired*/ ctx[2];

    			if (dirty & /*$$scope, project, alreadyAdded*/ 8195) {
    				projectbuttonbase_changes.$$scope = { dirty, ctx };
    			}

    			projectbuttonbase.$set(projectbuttonbase_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projectbuttonbase.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectbuttonbase.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projectbuttonbase, detaching);
    		}
    	};
    }

    function instance$c($$self, $$props, $$invalidate) {
    	let $isPackageManagerRequired;
    	component_subscribe($$self, isPackageManagerRequired, $$value => $$invalidate(2, $isPackageManagerRequired = $$value));
    	let { project } = $$props;
    	let { loading } = $$props;
    	let { projectInstalled } = $$props;
    	let { projectDownloaded } = $$props;
    	let { showStatus } = $$props;
    	let { alreadyAdded = false } = $$props;

    	const handleError = async errorResponse => {
    		// If an error occurred, set loading to false so the UI no longer reports
    		// the download/install as in progress.
    		$$invalidate(5, loading = false);

    		// The error can take on many shapes, so it should be normalized.
    		let err = '';

    		if (typeof errorResponse === 'string') {
    			err = errorResponse;
    		} else {
    			err = await errorResponse.text();
    		}

    		try {
    			// See if the error string can be parsed as JSON. If not, the block
    			// is exited before the `err` string is overwritten.
    			const parsed = JSON.parse(err);

    			err = parsed;
    		} catch(error) {
    			
    		} // The catch behavior is established before the try block.

    		const errorMessage = err.message || err;

    		// The popup function expects an element, so a div containing the error
    		// message is created here for it to display in a modal.
    		const div = document.createElement('div');

    		if (err.unlock_url && err.unlock_url !== '') {
    			div.innerHTML += `<p>${errorMessage} <a href="${err.unlock_url}&destination=admin/modules/browse">${window.Drupal.t('Unlock Install Stage')}</a></p>`;
    		} else {
    			div.innerHTML += `<p>${errorMessage}</p>`;
    		}

    		openPopup(div, {
    			...project,
    			title: `Error while installing ${project.title}`
    		});
    	};

    	/**
     * Installs an already downloaded module.
     */
    	async function installModule() {
    		$$invalidate(5, loading = true);
    		const url = `${ORIGIN_URL}/admin/modules/project_browser/activate/${project.id}`;
    		const installResponse = await fetch(url);

    		if (!installResponse.ok) {
    			handleError(installResponse);
    			$$invalidate(5, loading = false);
    			return;
    		}

    		let responseContent = await installResponse.text();

    		try {
    			const parsedJson = JSON.parse(responseContent);
    			responseContent = parsedJson;
    		} catch(err) {
    			handleError(installResponse);
    		}

    		if (responseContent.status === 0) {
    			MODULE_STATUS[project.project_machine_name] = 1;
    			$$invalidate(6, projectInstalled = true);
    			$$invalidate(5, loading = false);
    		}
    	}

    	/**
     * Uses package manager to download a module using Composer.
     *
     * @param {boolean} install
     *   If true, the module will be installed after it is downloaded.
     */
    	function downloadModule(install = false) {
    		showStatus(true);

    		/**
     * Performs the requests necessary to download a module via Package Manager.
     *
     * @return {Promise<void>}
     *   No return, but is technically a Promise because this function is async.
     */
    		async function doRequests() {
    			$$invalidate(5, loading = true);
    			const beginInstallUrl = `${ORIGIN_URL}/admin/modules/project_browser/install-begin/${project.id}`;
    			const beginInstallResponse = await fetch(beginInstallUrl);

    			if (!beginInstallResponse.ok) {
    				await handleError(beginInstallResponse);
    			} else {
    				await beginInstallResponse.json();

    				// The process of adding a module is separated into four stages, each
    				// with their own endpoint. When one stage completes, the next one is
    				// requested.
    				const installSteps = [
    					`${ORIGIN_URL}/admin/modules/project_browser/install-require/${project.id}`,
    					`${ORIGIN_URL}/admin/modules/project_browser/install-apply`,
    					`${ORIGIN_URL}/admin/modules/project_browser/install-post_apply`,
    					`${ORIGIN_URL}/admin/modules/project_browser/install-destroy`
    				];

    				// eslint-disable-next-line no-restricted-syntax,guard-for-in
    				for (const step in installSteps) {
    					// eslint-disable-next-line no-await-in-loop
    					const stepResponse = await fetch(installSteps[step]);

    					if (!stepResponse.ok) {
    						// eslint-disable-next-line no-await-in-loop
    						const errorMessage = await stepResponse.text();

    						// eslint-disable-next-line no-console
    						console.warn(`failed request to ${installSteps[step]}: ${errorMessage}`, stepResponse);

    						// eslint-disable-next-line no-await-in-loop
    						await handleError(errorMessage);

    						return;
    					}
    				}

    				// If this line is reached, then every stage of the download process
    				// was completed without error and we can consider the module
    				// downloaded and the process complete.
    				MODULE_STATUS[project.project_machine_name] = 0;

    				$$invalidate(7, projectDownloaded = true);
    				$$invalidate(5, loading = false);

    				// If install is true, install the module before conveying the process
    				// is complete to the UI.
    				if (install === true) {
    					installModule();
    				}
    			}
    		}

    		// Begin the install process, which is contained in the doRequests()
    		// function so it can be async without its parent function having to be.
    		doRequests();
    	}

    	const func = () => {
    		if (alreadyAdded) {
    			installModule();
    		} else {
    			downloadModule(true);
    		}
    	};

    	$$self.$$set = $$props => {
    		if ('project' in $$props) $$invalidate(0, project = $$props.project);
    		if ('loading' in $$props) $$invalidate(5, loading = $$props.loading);
    		if ('projectInstalled' in $$props) $$invalidate(6, projectInstalled = $$props.projectInstalled);
    		if ('projectDownloaded' in $$props) $$invalidate(7, projectDownloaded = $$props.projectDownloaded);
    		if ('showStatus' in $$props) $$invalidate(8, showStatus = $$props.showStatus);
    		if ('alreadyAdded' in $$props) $$invalidate(1, alreadyAdded = $$props.alreadyAdded);
    	};

    	return [
    		project,
    		alreadyAdded,
    		$isPackageManagerRequired,
    		installModule,
    		downloadModule,
    		loading,
    		projectInstalled,
    		projectDownloaded,
    		showStatus,
    		func
    	];
    }

    class AddInstallButton extends SvelteComponent {
    	constructor(options) {
    		super();

    		init(this, options, instance$c, create_fragment$c, safe_not_equal, {
    			project: 0,
    			loading: 5,
    			projectInstalled: 6,
    			projectDownloaded: 7,
    			showStatus: 8,
    			alreadyAdded: 1
    		});
    	}
    }

    /* src/Project/LoadingEllipsis.svelte generated by Svelte v3.48.0 */

    function create_fragment$b(ctx) {
    	let span;
    	let t;

    	return {
    		c() {
    			span = element("span");
    			t = text(/*message*/ ctx[0]);
    			attr(span, "class", "pb-ellipsis");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    			append(span, t);
    		},
    		p(ctx, [dirty]) {
    			if (dirty & /*message*/ 1) set_data(t, /*message*/ ctx[0]);
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(span);
    		}
    	};
    }

    function instance$b($$self, $$props, $$invalidate) {
    	let { message = window.Drupal.t('Installing') } = $$props;

    	$$self.$$set = $$props => {
    		if ('message' in $$props) $$invalidate(0, message = $$props.message);
    	};

    	return [message];
    }

    class LoadingEllipsis extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$b, create_fragment$b, safe_not_equal, { message: 0 });
    	}
    }

    /* src/Project/ProjectStatusIndicator.svelte generated by Svelte v3.48.0 */

    function create_fragment$a(ctx) {
    	let span1;
    	let t0;
    	let span0;
    	let t1_value = window.Drupal.t('@module is', { '@module': `${/*project*/ ctx[0].title}` }) + "";
    	let t1;
    	let t2;
    	let t3;
    	let current;
    	const default_slot_template = /*#slots*/ ctx[3].default;
    	const default_slot = create_slot(default_slot_template, ctx, /*$$scope*/ ctx[2], null);

    	return {
    		c() {
    			span1 = element("span");
    			if (default_slot) default_slot.c();
    			t0 = space();
    			span0 = element("span");
    			t1 = text(t1_value);
    			t2 = space();
    			t3 = text(/*statusText*/ ctx[1]);
    			attr(span0, "class", "visually-hidden");
    			attr(span1, "class", "project_status-indicator");
    		},
    		m(target, anchor) {
    			insert(target, span1, anchor);

    			if (default_slot) {
    				default_slot.m(span1, null);
    			}

    			append(span1, t0);
    			append(span1, span0);
    			append(span0, t1);
    			append(span1, t2);
    			append(span1, t3);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if (default_slot) {
    				if (default_slot.p && (!current || dirty & /*$$scope*/ 4)) {
    					update_slot_base(
    						default_slot,
    						default_slot_template,
    						ctx,
    						/*$$scope*/ ctx[2],
    						!current
    						? get_all_dirty_from_scope(/*$$scope*/ ctx[2])
    						: get_slot_changes(default_slot_template, /*$$scope*/ ctx[2], dirty, null),
    						null
    					);
    				}
    			}

    			if ((!current || dirty & /*project*/ 1) && t1_value !== (t1_value = window.Drupal.t('@module is', { '@module': `${/*project*/ ctx[0].title}` }) + "")) set_data(t1, t1_value);
    			if (!current || dirty & /*statusText*/ 2) set_data(t3, /*statusText*/ ctx[1]);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(default_slot, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(default_slot, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(span1);
    			if (default_slot) default_slot.d(detaching);
    		}
    	};
    }

    function instance$a($$self, $$props, $$invalidate) {
    	let { $$slots: slots = {}, $$scope } = $$props;
    	let { project } = $$props;
    	let { statusText } = $$props;

    	$$self.$$set = $$props => {
    		if ('project' in $$props) $$invalidate(0, project = $$props.project);
    		if ('statusText' in $$props) $$invalidate(1, statusText = $$props.statusText);
    		if ('$$scope' in $$props) $$invalidate(2, $$scope = $$props.$$scope);
    	};

    	return [project, statusText, $$scope, slots];
    }

    class ProjectStatusIndicator extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$a, create_fragment$a, safe_not_equal, { project: 0, statusText: 1 });
    	}
    }

    /* src/Project/ActionButton.svelte generated by Svelte v3.48.0 */

    function create_else_block_2(ctx) {
    	let span;
    	let current_block_type_index;
    	let if_block;
    	let current;
    	const if_block_creators = [create_if_block_5$1, create_if_block_7];
    	const if_blocks = [];

    	function select_block_type_3(ctx, dirty) {
    		if (!PM_VALIDATION_ERROR && ALLOW_UI_INSTALL) return 0;
    		if (/*project*/ ctx[0].type === 'module:drupalorg' || /*project*/ ctx[0].commands) return 1;
    		return -1;
    	}

    	if (~(current_block_type_index = select_block_type_3(ctx))) {
    		if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    	}

    	return {
    		c() {
    			span = element("span");
    			if (if_block) if_block.c();
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);

    			if (~current_block_type_index) {
    				if_blocks[current_block_type_index].m(span, null);
    			}

    			current = true;
    		},
    		p(ctx, dirty) {
    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type_3(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if (~current_block_type_index) {
    					if_blocks[current_block_type_index].p(ctx, dirty);
    				}
    			} else {
    				if (if_block) {
    					group_outros();

    					transition_out(if_blocks[previous_block_index], 1, 1, () => {
    						if_blocks[previous_block_index] = null;
    					});

    					check_outros();
    				}

    				if (~current_block_type_index) {
    					if_block = if_blocks[current_block_type_index];

    					if (!if_block) {
    						if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    						if_block.c();
    					} else {
    						if_block.p(ctx, dirty);
    					}

    					transition_in(if_block, 1);
    					if_block.m(span, null);
    				} else {
    					if_block = null;
    				}
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(span);

    			if (~current_block_type_index) {
    				if_blocks[current_block_type_index].d();
    			}
    		}
    	};
    }

    // (187:30) 
    function create_if_block_2$4(ctx) {
    	let span;
    	let current_block_type_index;
    	let if_block;
    	let current;
    	const if_block_creators = [create_if_block_3$2, create_else_block_1$2];
    	const if_blocks = [];

    	function select_block_type_1(ctx, dirty) {
    		if (ALLOW_UI_INSTALL) return 0;
    		return 1;
    	}

    	current_block_type_index = select_block_type_1();
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	return {
    		c() {
    			span = element("span");
    			if_block.c();
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    			if_blocks[current_block_type_index].m(span, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if_block.p(ctx, dirty);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(span);
    			if_blocks[current_block_type_index].d();
    		}
    	};
    }

    // (183:29) 
    function create_if_block_1$7(ctx) {
    	let projectstatusindicator;
    	let current;

    	projectstatusindicator = new ProjectStatusIndicator({
    			props: {
    				project: /*project*/ ctx[0],
    				statusText: window.Drupal.t('Installed'),
    				$$slots: { default: [create_default_slot$1] },
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			create_component(projectstatusindicator.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(projectstatusindicator, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const projectstatusindicator_changes = {};
    			if (dirty & /*project*/ 1) projectstatusindicator_changes.project = /*project*/ ctx[0];

    			if (dirty & /*$$scope*/ 131072) {
    				projectstatusindicator_changes.$$scope = { dirty, ctx };
    			}

    			projectstatusindicator.$set(projectstatusindicator_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projectstatusindicator.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectstatusindicator.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projectstatusindicator, detaching);
    		}
    	};
    }

    // (181:2) {#if !project.is_compatible}
    function create_if_block$8(ctx) {
    	let projectstatusindicator;
    	let current;

    	projectstatusindicator = new ProjectStatusIndicator({
    			props: {
    				project: /*project*/ ctx[0],
    				statusText: window.Drupal.t('Not compatible')
    			}
    		});

    	return {
    		c() {
    			create_component(projectstatusindicator.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(projectstatusindicator, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const projectstatusindicator_changes = {};
    			if (dirty & /*project*/ 1) projectstatusindicator_changes.project = /*project*/ ctx[0];
    			projectstatusindicator.$set(projectstatusindicator_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projectstatusindicator.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectstatusindicator.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projectstatusindicator, detaching);
    		}
    	};
    }

    // (227:72) 
    function create_if_block_7(ctx) {
    	let projectbuttonbase;
    	let current;

    	projectbuttonbase = new ProjectButtonBase({
    			props: {
    				click: /*func*/ ctx[12],
    				$$slots: { default: [create_default_slot_2] },
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			create_component(projectbuttonbase.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(projectbuttonbase, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const projectbuttonbase_changes = {};
    			if (dirty & /*project*/ 1) projectbuttonbase_changes.click = /*func*/ ctx[12];

    			if (dirty & /*$$scope, project*/ 131073) {
    				projectbuttonbase_changes.$$scope = { dirty, ctx };
    			}

    			projectbuttonbase.$set(projectbuttonbase_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projectbuttonbase.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectbuttonbase.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projectbuttonbase, detaching);
    		}
    	};
    }

    // (214:6) {#if !PM_VALIDATION_ERROR && ALLOW_UI_INSTALL}
    function create_if_block_5$1(ctx) {
    	let current_block_type_index;
    	let if_block;
    	let if_block_anchor;
    	let current;
    	const if_block_creators = [create_if_block_6$1, create_else_block_3];
    	const if_blocks = [];

    	function select_block_type_4(ctx, dirty) {
    		if (/*loading*/ ctx[1]) return 0;
    		return 1;
    	}

    	current_block_type_index = select_block_type_4(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	return {
    		c() {
    			if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if_blocks[current_block_type_index].m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type_4(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if_blocks[current_block_type_index].p(ctx, dirty);
    			} else {
    				group_outros();

    				transition_out(if_blocks[previous_block_index], 1, 1, () => {
    					if_blocks[previous_block_index] = null;
    				});

    				check_outros();
    				if_block = if_blocks[current_block_type_index];

    				if (!if_block) {
    					if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    					if_block.c();
    				} else {
    					if_block.p(ctx, dirty);
    				}

    				transition_in(if_block, 1);
    				if_block.m(if_block_anchor.parentNode, if_block_anchor);
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if_blocks[current_block_type_index].d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (228:8) <ProjectButtonBase           click={() => openPopup(getCommandsPopupMessage(project), project)}           >
    function create_default_slot_2(ctx) {
    	let t0_value = window.Drupal.t('View Commands') + "";
    	let t0;
    	let t1;
    	let span;
    	let t2_value = window.Drupal.t(' for ') + "";
    	let t2;
    	let t3;
    	let t4_value = /*project*/ ctx[0].title + "";
    	let t4;

    	return {
    		c() {
    			t0 = text(t0_value);
    			t1 = space();
    			span = element("span");
    			t2 = text(t2_value);
    			t3 = space();
    			t4 = text(t4_value);
    			attr(span, "class", "visually-hidden");
    		},
    		m(target, anchor) {
    			insert(target, t0, anchor);
    			insert(target, t1, anchor);
    			insert(target, span, anchor);
    			append(span, t2);
    			append(span, t3);
    			append(span, t4);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*project*/ 1 && t4_value !== (t4_value = /*project*/ ctx[0].title + "")) set_data(t4, t4_value);
    		},
    		d(detaching) {
    			if (detaching) detach(t0);
    			if (detaching) detach(t1);
    			if (detaching) detach(span);
    		}
    	};
    }

    // (218:8) {:else}
    function create_else_block_3(ctx) {
    	let addinstallbutton;
    	let updating_loading;
    	let updating_projectInstalled;
    	let updating_projectDownloaded;
    	let current;

    	function addinstallbutton_loading_binding_1(value) {
    		/*addinstallbutton_loading_binding_1*/ ctx[9](value);
    	}

    	function addinstallbutton_projectInstalled_binding_1(value) {
    		/*addinstallbutton_projectInstalled_binding_1*/ ctx[10](value);
    	}

    	function addinstallbutton_projectDownloaded_binding_1(value) {
    		/*addinstallbutton_projectDownloaded_binding_1*/ ctx[11](value);
    	}

    	let addinstallbutton_props = {
    		project: /*project*/ ctx[0],
    		showStatus: /*showStatus*/ ctx[5]
    	};

    	if (/*loading*/ ctx[1] !== void 0) {
    		addinstallbutton_props.loading = /*loading*/ ctx[1];
    	}

    	if (/*projectInstalled*/ ctx[3] !== void 0) {
    		addinstallbutton_props.projectInstalled = /*projectInstalled*/ ctx[3];
    	}

    	if (/*projectDownloaded*/ ctx[4] !== void 0) {
    		addinstallbutton_props.projectDownloaded = /*projectDownloaded*/ ctx[4];
    	}

    	addinstallbutton = new AddInstallButton({ props: addinstallbutton_props });
    	binding_callbacks.push(() => bind(addinstallbutton, 'loading', addinstallbutton_loading_binding_1));
    	binding_callbacks.push(() => bind(addinstallbutton, 'projectInstalled', addinstallbutton_projectInstalled_binding_1));
    	binding_callbacks.push(() => bind(addinstallbutton, 'projectDownloaded', addinstallbutton_projectDownloaded_binding_1));

    	return {
    		c() {
    			create_component(addinstallbutton.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(addinstallbutton, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const addinstallbutton_changes = {};
    			if (dirty & /*project*/ 1) addinstallbutton_changes.project = /*project*/ ctx[0];

    			if (!updating_loading && dirty & /*loading*/ 2) {
    				updating_loading = true;
    				addinstallbutton_changes.loading = /*loading*/ ctx[1];
    				add_flush_callback(() => updating_loading = false);
    			}

    			if (!updating_projectInstalled && dirty & /*projectInstalled*/ 8) {
    				updating_projectInstalled = true;
    				addinstallbutton_changes.projectInstalled = /*projectInstalled*/ ctx[3];
    				add_flush_callback(() => updating_projectInstalled = false);
    			}

    			if (!updating_projectDownloaded && dirty & /*projectDownloaded*/ 16) {
    				updating_projectDownloaded = true;
    				addinstallbutton_changes.projectDownloaded = /*projectDownloaded*/ ctx[4];
    				add_flush_callback(() => updating_projectDownloaded = false);
    			}

    			addinstallbutton.$set(addinstallbutton_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(addinstallbutton.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(addinstallbutton.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(addinstallbutton, detaching);
    		}
    	};
    }

    // (215:8) {#if loading}
    function create_if_block_6$1(ctx) {
    	let loading_1;
    	let t0;
    	let span;
    	let t1;
    	let current;

    	loading_1 = new Loading({
    			props: { positionAbsolute: true, inline: true }
    		});

    	return {
    		c() {
    			create_component(loading_1.$$.fragment);
    			t0 = space();
    			span = element("span");
    			t1 = text(/*loadingPhase*/ ctx[2]);
    			attr(span, "class", "pb-ellipsis");
    		},
    		m(target, anchor) {
    			mount_component(loading_1, target, anchor);
    			insert(target, t0, anchor);
    			insert(target, span, anchor);
    			append(span, t1);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if (!current || dirty & /*loadingPhase*/ 4) set_data(t1, /*loadingPhase*/ ctx[2]);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(loading_1.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(loading_1.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(loading_1, detaching);
    			if (detaching) detach(t0);
    			if (detaching) detach(span);
    		}
    	};
    }

    // (203:6) {:else}
    function create_else_block_1$2(ctx) {
    	let a;
    	let projectbuttonbase;
    	let a_href_value;
    	let current;

    	projectbuttonbase = new ProjectButtonBase({
    			props: {
    				$$slots: { default: [create_default_slot_1$1] },
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			a = element("a");
    			create_component(projectbuttonbase.$$.fragment);
    			attr(a, "href", a_href_value = "" + (ORIGIN_URL + "/admin/modules#module-" + /*project*/ ctx[0].selector_id));
    			attr(a, "target", "_blank");
    			attr(a, "rel", "noreferrer");
    		},
    		m(target, anchor) {
    			insert(target, a, anchor);
    			mount_component(projectbuttonbase, a, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const projectbuttonbase_changes = {};

    			if (dirty & /*$$scope*/ 131072) {
    				projectbuttonbase_changes.$$scope = { dirty, ctx };
    			}

    			projectbuttonbase.$set(projectbuttonbase_changes);

    			if (!current || dirty & /*project*/ 1 && a_href_value !== (a_href_value = "" + (ORIGIN_URL + "/admin/modules#module-" + /*project*/ ctx[0].selector_id))) {
    				attr(a, "href", a_href_value);
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projectbuttonbase.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectbuttonbase.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(a);
    			destroy_component(projectbuttonbase);
    		}
    	};
    }

    // (189:6) {#if ALLOW_UI_INSTALL}
    function create_if_block_3$2(ctx) {
    	let current_block_type_index;
    	let if_block;
    	let if_block_anchor;
    	let current;
    	const if_block_creators = [create_if_block_4$2, create_else_block$2];
    	const if_blocks = [];

    	function select_block_type_2(ctx, dirty) {
    		if (/*loading*/ ctx[1]) return 0;
    		return 1;
    	}

    	current_block_type_index = select_block_type_2(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	return {
    		c() {
    			if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if_blocks[current_block_type_index].m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type_2(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if_blocks[current_block_type_index].p(ctx, dirty);
    			} else {
    				group_outros();

    				transition_out(if_blocks[previous_block_index], 1, 1, () => {
    					if_blocks[previous_block_index] = null;
    				});

    				check_outros();
    				if_block = if_blocks[current_block_type_index];

    				if (!if_block) {
    					if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    					if_block.c();
    				} else {
    					if_block.p(ctx, dirty);
    				}

    				transition_in(if_block, 1);
    				if_block.m(if_block_anchor.parentNode, if_block_anchor);
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if_blocks[current_block_type_index].d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (208:11) <ProjectButtonBase>
    function create_default_slot_1$1(ctx) {
    	let t_value = window.Drupal.t('Install') + "";
    	let t;

    	return {
    		c() {
    			t = text(t_value);
    		},
    		m(target, anchor) {
    			insert(target, t, anchor);
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(t);
    		}
    	};
    }

    // (193:8) {:else}
    function create_else_block$2(ctx) {
    	let addinstallbutton;
    	let updating_loading;
    	let updating_projectInstalled;
    	let updating_projectDownloaded;
    	let current;

    	function addinstallbutton_loading_binding(value) {
    		/*addinstallbutton_loading_binding*/ ctx[6](value);
    	}

    	function addinstallbutton_projectInstalled_binding(value) {
    		/*addinstallbutton_projectInstalled_binding*/ ctx[7](value);
    	}

    	function addinstallbutton_projectDownloaded_binding(value) {
    		/*addinstallbutton_projectDownloaded_binding*/ ctx[8](value);
    	}

    	let addinstallbutton_props = {
    		project: /*project*/ ctx[0],
    		showStatus: /*showStatus*/ ctx[5],
    		alreadyAdded: true
    	};

    	if (/*loading*/ ctx[1] !== void 0) {
    		addinstallbutton_props.loading = /*loading*/ ctx[1];
    	}

    	if (/*projectInstalled*/ ctx[3] !== void 0) {
    		addinstallbutton_props.projectInstalled = /*projectInstalled*/ ctx[3];
    	}

    	if (/*projectDownloaded*/ ctx[4] !== void 0) {
    		addinstallbutton_props.projectDownloaded = /*projectDownloaded*/ ctx[4];
    	}

    	addinstallbutton = new AddInstallButton({ props: addinstallbutton_props });
    	binding_callbacks.push(() => bind(addinstallbutton, 'loading', addinstallbutton_loading_binding));
    	binding_callbacks.push(() => bind(addinstallbutton, 'projectInstalled', addinstallbutton_projectInstalled_binding));
    	binding_callbacks.push(() => bind(addinstallbutton, 'projectDownloaded', addinstallbutton_projectDownloaded_binding));

    	return {
    		c() {
    			create_component(addinstallbutton.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(addinstallbutton, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const addinstallbutton_changes = {};
    			if (dirty & /*project*/ 1) addinstallbutton_changes.project = /*project*/ ctx[0];

    			if (!updating_loading && dirty & /*loading*/ 2) {
    				updating_loading = true;
    				addinstallbutton_changes.loading = /*loading*/ ctx[1];
    				add_flush_callback(() => updating_loading = false);
    			}

    			if (!updating_projectInstalled && dirty & /*projectInstalled*/ 8) {
    				updating_projectInstalled = true;
    				addinstallbutton_changes.projectInstalled = /*projectInstalled*/ ctx[3];
    				add_flush_callback(() => updating_projectInstalled = false);
    			}

    			if (!updating_projectDownloaded && dirty & /*projectDownloaded*/ 16) {
    				updating_projectDownloaded = true;
    				addinstallbutton_changes.projectDownloaded = /*projectDownloaded*/ ctx[4];
    				add_flush_callback(() => updating_projectDownloaded = false);
    			}

    			addinstallbutton.$set(addinstallbutton_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(addinstallbutton.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(addinstallbutton.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(addinstallbutton, detaching);
    		}
    	};
    }

    // (190:8) {#if loading}
    function create_if_block_4$2(ctx) {
    	let loading_1;
    	let t;
    	let loadingellipsis;
    	let current;

    	loading_1 = new Loading({
    			props: { positionAbsolute: true, inline: true }
    		});

    	loadingellipsis = new LoadingEllipsis({});

    	return {
    		c() {
    			create_component(loading_1.$$.fragment);
    			t = space();
    			create_component(loadingellipsis.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(loading_1, target, anchor);
    			insert(target, t, anchor);
    			mount_component(loadingellipsis, target, anchor);
    			current = true;
    		},
    		p: noop,
    		i(local) {
    			if (current) return;
    			transition_in(loading_1.$$.fragment, local);
    			transition_in(loadingellipsis.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(loading_1.$$.fragment, local);
    			transition_out(loadingellipsis.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(loading_1, detaching);
    			if (detaching) detach(t);
    			destroy_component(loadingellipsis, detaching);
    		}
    	};
    }

    // (184:4) <ProjectStatusIndicator {project} statusText={window.Drupal.t('Installed')}>
    function create_default_slot$1(ctx) {
    	let span;

    	return {
    		c() {
    			span = element("span");
    			span.textContent = "✓";
    			attr(span, "class", "pb-actions__icon");
    			attr(span, "aria-hidden", "true");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(span);
    		}
    	};
    }

    function create_fragment$9(ctx) {
    	let div;
    	let current_block_type_index;
    	let if_block;
    	let current;
    	const if_block_creators = [create_if_block$8, create_if_block_1$7, create_if_block_2$4, create_else_block_2];
    	const if_blocks = [];

    	function select_block_type(ctx, dirty) {
    		if (!/*project*/ ctx[0].is_compatible) return 0;
    		if (/*projectInstalled*/ ctx[3]) return 1;
    		if (/*projectDownloaded*/ ctx[4]) return 2;
    		return 3;
    	}

    	current_block_type_index = select_block_type(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	return {
    		c() {
    			div = element("div");
    			if_block.c();
    			attr(div, "class", "pb-actions");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			if_blocks[current_block_type_index].m(div, null);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if_blocks[current_block_type_index].p(ctx, dirty);
    			} else {
    				group_outros();

    				transition_out(if_blocks[previous_block_index], 1, 1, () => {
    					if_blocks[previous_block_index] = null;
    				});

    				check_outros();
    				if_block = if_blocks[current_block_type_index];

    				if (!if_block) {
    					if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    					if_block.c();
    				} else {
    					if_block.p(ctx, dirty);
    				}

    				transition_in(if_block, 1);
    				if_block.m(div, null);
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			if_blocks[current_block_type_index].d();
    		}
    	};
    }

    function instance$9($$self, $$props, $$invalidate) {
    	let { project } = $$props;
    	let loading = false;
    	let loadingPhase = 'Adding';
    	const { drupalSettings, Drupal } = window;

    	/**
     * Determine is a project is present in the local Drupal codebase.
     *
     * @param {string} projectName
     *    The project name.
     * @return {boolean}
     *   True if the project is present.
     */
    	function projectIsDownloaded(projectName) {
    		return typeof drupalSettings !== 'undefined' && projectName in MODULE_STATUS;
    	}

    	/**
     * Determine if a project is installed in the local Drupal codebase.
     *
     * @param {string} projectName
     *   The project name.
     * @return {boolean}
     *   True if the project is installed.
     */
    	function projectIsInstalled(projectName) {
    		return typeof drupalSettings !== 'undefined' && projectName in MODULE_STATUS && MODULE_STATUS[projectName] === 1;
    	}

    	let projectInstalled = projectIsInstalled(project.project_machine_name);
    	let projectDownloaded = projectIsDownloaded(project.project_machine_name);

    	/**
     * Checks the download/install status of a project and updates the UI.
     *
     * During an install, this function is repeatedly called to check the status
     * of the download/install operation, and the UI is updated with the stage the
     * process is currently in. This function stops being called when the process
     * successfully completes or stops due to an error.
     *
     * @param {boolean} initiate
     *   When true, begin the install process for the project.
     * @return {Promise<void>}
     *   Return is not used, but is a promise due to this being async.
     */
    	const showStatus = async (initiate = false) => {
    		const url = `${ORIGIN_URL}/admin/modules/project_browser/install_in_progress/${project.id}`;

    		//
    		/**
     * Gets the current status of the project's download or require process.
     *
     * @return {Promise<any>}
     *   The JSON status response, plus the timestamp of when it was returned.
     */
    		const status = async () => {
    			const progressCheck = await fetch(url);
    			const json = await progressCheck.json();
    			return { ...json, time: new Date().getTime() };
    		};

    		const loadingStatus = await status();

    		// We keep track of how many intervals have taken place during the progress
    		// check so we can announce progress to every 5-10 seconds.
    		let intervals = 0;

    		// When a require begins, there may be a delay before the
    		// `install_in_progress` endpoint provides the correct status. The
    		// initiateLag is how many times the interval below will check for that
    		// status before aborting.
    		let initiateLag = 4;

    		// The initiate variable means a new download or install was requested and
    		// the associate process should begin.
    		// The loadingStatus checks are for when project browser is loaded and one
    		// of the listed projects has a download or install in progress, so the UI
    		// conveys this even if the process was initiated in another tab or by a
    		// different user.
    		if (initiate || loadingStatus && loadingStatus.status !== 0) {
    			$$invalidate(1, loading = true);

    			$$invalidate(2, loadingPhase = loadingStatus.phase
    			? window.Drupal.t('Adding: @phase', { '@phase': loadingStatus.phase })
    			: window.Drupal.t('Installing'));

    			const intervalId = setInterval(
    				async () => {
    					const currentStatus = await status();
    					const notInProgress = currentStatus.status === 0 && !initiate;

    					// If the initiateLag is at 0, there's been sufficient time for the
    					// install controller to return a valid status. If that has not
    					// happened by then, there is likely an underlying issue that won't be
    					// addressed by waiting longer. We categorize this attempt to download /
    					// install as "initiated but never started" and clear the interval that
    					// is repeatedly invoking this function.
    					const initiatedButNeverStarted = (initiateLag === 0 || !currentStatus.hasOwnProperty('phase')) && initiate && !currentStatus.status === 0;

    					if (notInProgress || initiatedButNeverStarted || !loading) {
    						// The process has either completed, or encountered a problem that
    						// would not benefit from further iterations of this function. The
    						// interval is cleared and the UI is updated to indicate nothing is in
    						// progress.
    						clearInterval(intervalId);

    						$$invalidate(1, loading = false);
    					} else {
    						// During parts of the process where the Package Manager stage is in
    						// use, the status includes the phase of the process taking place.
    						// Use this when available, otherwise provide a default message.
    						$$invalidate(2, loadingPhase = currentStatus.phase || 'In progress');
    					}

    					initiateLag -= 1;

    					if (intervals % 4 === 1) {
    						// Clear announce in the interval immediately after a read so if
    						// announce is called again it will be conveyed to the screen reader
    						// even if the progress message is unchanged.
    						Drupal.announce('');
    					}

    					if (intervals === 0 || intervals % 4 === 0) {
    						if (currentStatus.phase) {
    							Drupal.announce(window.Drupal.t(
    								'Adding module @module, phase @phase in progress',
    								{
    									'@module': project.title,
    									'@phase': currentStatus.phase
    								},
    								'assertive'
    							));
    						} else {
    							Drupal.announce(window.Drupal.t('Adding module @module, in progress', { '@module': project.title }, 'assertive'));
    						}
    					}

    					intervals += 1;
    				},
    				1250
    			);
    		}
    	};

    	onMount(() => {
    		// If the module is mid-download or mid-install when the page loads, the UI
    		// should reflect that by adding a progress spinner and disabling actions.
    		// The app will check periodically to see if the status has changed and
    		// update the UI.
    		if (ALLOW_UI_INSTALL) {
    			showStatus();
    		}
    	});

    	function addinstallbutton_loading_binding(value) {
    		loading = value;
    		$$invalidate(1, loading);
    	}

    	function addinstallbutton_projectInstalled_binding(value) {
    		projectInstalled = value;
    		$$invalidate(3, projectInstalled);
    	}

    	function addinstallbutton_projectDownloaded_binding(value) {
    		projectDownloaded = value;
    		$$invalidate(4, projectDownloaded);
    	}

    	function addinstallbutton_loading_binding_1(value) {
    		loading = value;
    		$$invalidate(1, loading);
    	}

    	function addinstallbutton_projectInstalled_binding_1(value) {
    		projectInstalled = value;
    		$$invalidate(3, projectInstalled);
    	}

    	function addinstallbutton_projectDownloaded_binding_1(value) {
    		projectDownloaded = value;
    		$$invalidate(4, projectDownloaded);
    	}

    	const func = () => openPopup(getCommandsPopupMessage(project), project);

    	$$self.$$set = $$props => {
    		if ('project' in $$props) $$invalidate(0, project = $$props.project);
    	};

    	return [
    		project,
    		loading,
    		loadingPhase,
    		projectInstalled,
    		projectDownloaded,
    		showStatus,
    		addinstallbutton_loading_binding,
    		addinstallbutton_projectInstalled_binding,
    		addinstallbutton_projectDownloaded_binding,
    		addinstallbutton_loading_binding_1,
    		addinstallbutton_projectInstalled_binding_1,
    		addinstallbutton_projectDownloaded_binding_1,
    		func
    	];
    }

    class ActionButton extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$9, create_fragment$9, safe_not_equal, { project: 0 });
    	}
    }

    /* src/Project/Image.svelte generated by Svelte v3.48.0 */

    function create_else_block_1$1(ctx) {
    	let img;
    	let img_levels = [/*defaultImgProps*/ ctx[5](/*fallbackImage*/ ctx[3])];
    	let img_data = {};

    	for (let i = 0; i < img_levels.length; i += 1) {
    		img_data = assign(img_data, img_levels[i]);
    	}

    	return {
    		c() {
    			img = element("img");
    			set_attributes(img, img_data);
    		},
    		m(target, anchor) {
    			insert(target, img, anchor);
    		},
    		p(ctx, dirty) {
    			set_attributes(img, img_data = get_spread_update(img_levels, [/*defaultImgProps*/ ctx[5](/*fallbackImage*/ ctx[3])]));
    		},
    		d(detaching) {
    			if (detaching) detach(img);
    		}
    	};
    }

    // (44:0) {#if normalizedSources.length}
    function create_if_block$7(ctx) {
    	let if_block_anchor;

    	function select_block_type_1(ctx, dirty) {
    		if (/*normalizedSources*/ ctx[2][/*index*/ ctx[1]].file.resource === 'image') return create_if_block_1$6;
    		if (/*normalizedSources*/ ctx[2][/*index*/ ctx[1]].file.resource = 'file') return create_if_block_2$3;
    		return create_else_block$1;
    	}

    	let current_block_type = select_block_type_1(ctx);
    	let if_block = current_block_type(ctx);

    	return {
    		c() {
    			if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    		},
    		p(ctx, dirty) {
    			if (current_block_type === (current_block_type = select_block_type_1(ctx)) && if_block) {
    				if_block.p(ctx, dirty);
    			} else {
    				if_block.d(1);
    				if_block = current_block_type(ctx);

    				if (if_block) {
    					if_block.c();
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			}
    		},
    		d(detaching) {
    			if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (61:2) {:else}
    function create_else_block$1(ctx) {
    	let img;
    	let img_levels = [/*defaultImgProps*/ ctx[5](/*fallbackImage*/ ctx[3])];
    	let img_data = {};

    	for (let i = 0; i < img_levels.length; i += 1) {
    		img_data = assign(img_data, img_levels[i]);
    	}

    	return {
    		c() {
    			img = element("img");
    			set_attributes(img, img_data);
    		},
    		m(target, anchor) {
    			insert(target, img, anchor);
    		},
    		p(ctx, dirty) {
    			set_attributes(img, img_data = get_spread_update(img_levels, [/*defaultImgProps*/ ctx[5](/*fallbackImage*/ ctx[3])]));
    		},
    		d(detaching) {
    			if (detaching) detach(img);
    		}
    	};
    }

    // (52:62) 
    function create_if_block_2$3(ctx) {
    	let await_block_anchor;
    	let promise;

    	let info = {
    		ctx,
    		current: null,
    		token: null,
    		hasCatch: true,
    		pending: create_pending_block$1,
    		then: create_then_block$1,
    		catch: create_catch_block$1,
    		value: 9,
    		error: 10
    	};

    	handle_promise(promise = fetchEntity(/*normalizedSources*/ ctx[2][/*index*/ ctx[1]].file.uri), info);

    	return {
    		c() {
    			await_block_anchor = empty();
    			info.block.c();
    		},
    		m(target, anchor) {
    			insert(target, await_block_anchor, anchor);
    			info.block.m(target, info.anchor = anchor);
    			info.mount = () => await_block_anchor.parentNode;
    			info.anchor = await_block_anchor;
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			info.ctx = ctx;

    			if (dirty & /*index*/ 2 && promise !== (promise = fetchEntity(/*normalizedSources*/ ctx[2][/*index*/ ctx[1]].file.uri)) && handle_promise(promise, info)) ; else {
    				update_await_block_branch(info, ctx, dirty);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(await_block_anchor);
    			info.block.d(detaching);
    			info.token = null;
    			info = null;
    		}
    	};
    }

    // (45:2) {#if normalizedSources[index].file.resource === 'image'}
    function create_if_block_1$6(ctx) {
    	let img;
    	let img_src_value;
    	let img_class_value;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			img = element("img");
    			if (!src_url_equal(img.src, img_src_value = /*normalizedSources*/ ctx[2][/*index*/ ctx[1]].file.uri)) attr(img, "src", img_src_value);
    			attr(img, "alt", "");
    			attr(img, "class", img_class_value = /*$$props*/ ctx[6].class);
    		},
    		m(target, anchor) {
    			insert(target, img, anchor);

    			if (!mounted) {
    				dispose = listen(img, "error", /*showFallback*/ ctx[4]);
    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			if (dirty & /*index*/ 2 && !src_url_equal(img.src, img_src_value = /*normalizedSources*/ ctx[2][/*index*/ ctx[1]].file.uri)) {
    				attr(img, "src", img_src_value);
    			}

    			if (dirty & /*$$props*/ 64 && img_class_value !== (img_class_value = /*$$props*/ ctx[6].class)) {
    				attr(img, "class", img_class_value);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(img);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (58:4) {:catch error}
    function create_catch_block$1(ctx) {
    	let span;
    	let t_value = /*error*/ ctx[10].message + "";
    	let t;

    	return {
    		c() {
    			span = element("span");
    			t = text(t_value);
    			attr(span, "class", "image_error");
    			set_style(span, "color", "red");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    			append(span, t);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*index*/ 2 && t_value !== (t_value = /*error*/ ctx[10].message + "")) set_data(t, t_value);
    		},
    		d(detaching) {
    			if (detaching) detach(span);
    		}
    	};
    }

    // (56:4) {:then file}
    function create_then_block$1(ctx) {
    	let img;
    	let mounted;
    	let dispose;
    	let img_levels = [/*defaultImgProps*/ ctx[5](/*file*/ ctx[9].url, ''), { alt: "" }];
    	let img_data = {};

    	for (let i = 0; i < img_levels.length; i += 1) {
    		img_data = assign(img_data, img_levels[i]);
    	}

    	return {
    		c() {
    			img = element("img");
    			set_attributes(img, img_data);
    		},
    		m(target, anchor) {
    			insert(target, img, anchor);

    			if (!mounted) {
    				dispose = listen(img, "error", /*showFallback*/ ctx[4]);
    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			set_attributes(img, img_data = get_spread_update(img_levels, [
    				dirty & /*index*/ 2 && /*defaultImgProps*/ ctx[5](/*file*/ ctx[9].url, ''),
    				{ alt: "" }
    			]));
    		},
    		d(detaching) {
    			if (detaching) detach(img);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (54:59)        <img {...defaultImgProps(fallbackImage)}
    function create_pending_block$1(ctx) {
    	let img;
    	let img_levels = [/*defaultImgProps*/ ctx[5](/*fallbackImage*/ ctx[3])];
    	let img_data = {};

    	for (let i = 0; i < img_levels.length; i += 1) {
    		img_data = assign(img_data, img_levels[i]);
    	}

    	return {
    		c() {
    			img = element("img");
    			set_attributes(img, img_data);
    		},
    		m(target, anchor) {
    			insert(target, img, anchor);
    		},
    		p(ctx, dirty) {
    			set_attributes(img, img_data = get_spread_update(img_levels, [/*defaultImgProps*/ ctx[5](/*fallbackImage*/ ctx[3])]));
    		},
    		d(detaching) {
    			if (detaching) detach(img);
    		}
    	};
    }

    function create_fragment$8(ctx) {
    	let if_block_anchor;

    	function select_block_type(ctx, dirty) {
    		if (/*normalizedSources*/ ctx[2].length) return create_if_block$7;
    		return create_else_block_1$1;
    	}

    	let current_block_type = select_block_type(ctx);
    	let if_block = current_block_type(ctx);

    	return {
    		c() {
    			if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    		},
    		p(ctx, [dirty]) {
    			if_block.p(ctx, dirty);
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    async function fetchEntity(uri) {
    	let data;
    	const response = await fetch(`${uri}.json`);

    	if (response.ok) {
    		data = await response.json();
    		return data;
    	}

    	throw new Error('Could not load entity');
    }

    function instance$8($$self, $$props, $$invalidate) {
    	let { sources } = $$props;
    	let { index = 0 } = $$props;
    	const normalizedSources = sources ? [sources].flat() : [];
    	const fallbackImage = `${FULL_MODULE_PATH}/images/puzzle-piece-placeholder.svg`;

    	const showFallback = ev => {
    		ev.target.src = fallbackImage;
    	};

    	/**
     * Props for the images used in the carousel.
     *
     * @param {string} src
     *   The source attribute.
     * @param {string} alt
     *   The alt attribute, defaults to 'Placeholder' if undefined.
     *
     * @return {{src, alt: string, class: string}}
     *   An object of element attributes
     */
    	const defaultImgProps = (src, alt) => ({
    		src,
    		alt: typeof alt !== 'undefined'
    		? alt
    		: window.Drupal.t('Placeholder'),
    		class: `${$$props.class} `
    	});

    	$$self.$$set = $$new_props => {
    		$$invalidate(6, $$props = assign(assign({}, $$props), exclude_internal_props($$new_props)));
    		if ('sources' in $$new_props) $$invalidate(7, sources = $$new_props.sources);
    		if ('index' in $$new_props) $$invalidate(1, index = $$new_props.index);
    	};

    	$$props = exclude_internal_props($$props);

    	return [
    		fetchEntity,
    		index,
    		normalizedSources,
    		fallbackImage,
    		showFallback,
    		defaultImgProps,
    		$$props,
    		sources
    	];
    }

    class Image extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$8, create_fragment$8, safe_not_equal, { fetchEntity: 0, sources: 7, index: 1 });
    	}

    	get fetchEntity() {
    		return fetchEntity;
    	}
    }

    /* src/Project/Categories.svelte generated by Svelte v3.48.0 */

    function get_each_context$4(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[3] = list[i];
    	return child_ctx;
    }

    // (17:2) {#if typeof moduleCategories !== 'undefined' && moduleCategories.length}
    function create_if_block$6(ctx) {
    	let ul;
    	let each_value = /*moduleCategories*/ ctx[0] || [];
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$4(get_each_context$4(ctx, each_value, i));
    	}

    	return {
    		c() {
    			ul = element("ul");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			attr(ul, "class", "pb-project-categories__list");
    			toggle_class(ul, "pb-project-categories__list--centered", /*toggleView*/ ctx[1] === 'Grid');
    		},
    		m(target, anchor) {
    			insert(target, ul, anchor);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(ul, null);
    			}
    		},
    		p(ctx, dirty) {
    			if (dirty & /*moduleCategories*/ 1) {
    				each_value = /*moduleCategories*/ ctx[0] || [];
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$4(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$4(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(ul, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}

    			if (dirty & /*toggleView*/ 2) {
    				toggle_class(ul, "pb-project-categories__list--centered", /*toggleView*/ ctx[1] === 'Grid');
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(ul);
    			destroy_each(each_blocks, detaching);
    		}
    	};
    }

    // (22:6) {#each moduleCategories || [] as category}
    function create_each_block$4(ctx) {
    	let li;
    	let t0_value = /*category*/ ctx[3].name + "";
    	let t0;
    	let t1;

    	return {
    		c() {
    			li = element("li");
    			t0 = text(t0_value);
    			t1 = space();
    			attr(li, "class", "pb-project-categories__item");
    			toggle_class(li, "pb-project-categories__item--extra", /*category*/ ctx[3].id === 'overflow');
    		},
    		m(target, anchor) {
    			insert(target, li, anchor);
    			append(li, t0);
    			append(li, t1);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*moduleCategories*/ 1 && t0_value !== (t0_value = /*category*/ ctx[3].name + "")) set_data(t0, t0_value);

    			if (dirty & /*moduleCategories*/ 1) {
    				toggle_class(li, "pb-project-categories__item--extra", /*category*/ ctx[3].id === 'overflow');
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(li);
    		}
    	};
    }

    function create_fragment$7(ctx) {
    	let div;
    	let if_block = typeof /*moduleCategories*/ ctx[0] !== 'undefined' && /*moduleCategories*/ ctx[0].length && create_if_block$6(ctx);

    	return {
    		c() {
    			div = element("div");
    			if (if_block) if_block.c();
    			attr(div, "class", "pb-project-categories");
    			attr(div, "data-label", "Categories");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			if (if_block) if_block.m(div, null);
    		},
    		p(ctx, [dirty]) {
    			if (typeof /*moduleCategories*/ ctx[0] !== 'undefined' && /*moduleCategories*/ ctx[0].length) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block$6(ctx);
    					if_block.c();
    					if_block.m(div, null);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(div);
    			if (if_block) if_block.d();
    		}
    	};
    }

    function instance$7($$self, $$props, $$invalidate) {
    	let { moduleCategories } = $$props;
    	let { toggleView } = $$props;
    	const extraCategories = moduleCategories.splice(3);

    	if (extraCategories.length) {
    		const overflowText = window.Drupal.t('+ @count more', { '@count': extraCategories.length });
    		moduleCategories.push({ id: 'overflow', name: overflowText });
    	}

    	$$self.$$set = $$props => {
    		if ('moduleCategories' in $$props) $$invalidate(0, moduleCategories = $$props.moduleCategories);
    		if ('toggleView' in $$props) $$invalidate(1, toggleView = $$props.toggleView);
    	};

    	return [moduleCategories, toggleView];
    }

    class Categories extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$7, create_fragment$7, safe_not_equal, { moduleCategories: 0, toggleView: 1 });
    	}
    }

    /* src/Project/ProjectIcon.svelte generated by Svelte v3.48.0 */

    function create_fragment$6(ctx) {
    	let img;
    	let img_src_value;
    	let img_class_value;
    	let img_alt_value;

    	return {
    		c() {
    			img = element("img");
    			if (!src_url_equal(img.src, img_src_value = "" + (FULL_MODULE_PATH + "/images/" + /*typeToImg*/ ctx[3][/*type*/ ctx[0]].path + (DARK_COLOR_SCHEME ? '--dark-color-scheme' : '') + ".svg"))) attr(img, "src", img_src_value);
    			attr(img, "class", img_class_value = `pb-icon pb-icon--${/*variant*/ ctx[1]} pb-icon--${/*type*/ ctx[0]} ${/*classes*/ ctx[2]}`);
    			attr(img, "alt", img_alt_value = /*typeToImg*/ ctx[3][/*type*/ ctx[0]].path.alt);
    		},
    		m(target, anchor) {
    			insert(target, img, anchor);
    		},
    		p(ctx, [dirty]) {
    			if (dirty & /*type*/ 1 && !src_url_equal(img.src, img_src_value = "" + (FULL_MODULE_PATH + "/images/" + /*typeToImg*/ ctx[3][/*type*/ ctx[0]].path + (DARK_COLOR_SCHEME ? '--dark-color-scheme' : '') + ".svg"))) {
    				attr(img, "src", img_src_value);
    			}

    			if (dirty & /*variant, type, classes*/ 7 && img_class_value !== (img_class_value = `pb-icon pb-icon--${/*variant*/ ctx[1]} pb-icon--${/*type*/ ctx[0]} ${/*classes*/ ctx[2]}`)) {
    				attr(img, "class", img_class_value);
    			}

    			if (dirty & /*type*/ 1 && img_alt_value !== (img_alt_value = /*typeToImg*/ ctx[3][/*type*/ ctx[0]].path.alt)) {
    				attr(img, "alt", img_alt_value);
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (detaching) detach(img);
    		}
    	};
    }

    function instance$6($$self, $$props, $$invalidate) {
    	let { type = '' } = $$props;
    	let { variant = false } = $$props;
    	let { classes = false } = $$props;

    	const typeToImg = {
    		status: {
    			path: 'blue-security-shield-icon',
    			alt: window.Drupal.t('Security Advisory Coverage')
    		},
    		usage: {
    			path: 'project-usage-icon',
    			alt: window.Drupal.t('Project Usage')
    		},
    		compatible: {
    			path: 'compatible-icon',
    			alt: window.Drupal.t('Compatible')
    		}
    	};

    	$$self.$$set = $$props => {
    		if ('type' in $$props) $$invalidate(0, type = $$props.type);
    		if ('variant' in $$props) $$invalidate(1, variant = $$props.variant);
    		if ('classes' in $$props) $$invalidate(2, classes = $$props.classes);
    	};

    	return [type, variant, classes, typeToImg];
    }

    class ProjectIcon extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$6, create_fragment$6, safe_not_equal, { type: 0, variant: 1, classes: 2 });
    	}
    }

    /* src/Project/Project.svelte generated by Svelte v3.48.0 */

    function get_each_context$3(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[8] = list[i];
    	return child_ctx;
    }

    // (49:4) {#if project.is_covered}
    function create_if_block_5(ctx) {
    	let span;
    	let projecticon;
    	let t;
    	let current;
    	projecticon = new ProjectIcon({ props: { type: "status" } });
    	let if_block = /*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0 && create_if_block_6();

    	return {
    		c() {
    			span = element("span");
    			create_component(projecticon.$$.fragment);
    			t = space();
    			if (if_block) if_block.c();
    			attr(span, "class", "pb-project__status-icon");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    			mount_component(projecticon, span, null);
    			append(span, t);
    			if (if_block) if_block.m(span, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if (/*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block_6();
    					if_block.c();
    					if_block.m(span, null);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projecticon.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projecticon.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(span);
    			destroy_component(projecticon);
    			if (if_block) if_block.d();
    		}
    	};
    }

    // (54:8) {#if project.warnings && project.warnings.length > 0}
    function create_if_block_6(ctx) {
    	let small;

    	return {
    		c() {
    			small = element("small");
    			small.textContent = `${window.Drupal.t('Covered by the security advisory policy')}`;
    		},
    		m(target, anchor) {
    			insert(target, small, anchor);
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(small);
    		}
    	};
    }

    // (59:4) {#if toggleView === 'Grid' && project.project_usage_total !== -1}
    function create_if_block_4$1(ctx) {
    	let div;
    	let span;

    	let t_value = window.Drupal.t('@count installs ', {
    		'@count': /*project*/ ctx[0].project_usage_total.toLocaleString()
    	}) + "";

    	let t;

    	return {
    		c() {
    			div = element("div");
    			span = element("span");
    			t = text(t_value);
    			attr(span, "class", "pb-project__install-count");
    			attr(div, "class", "pb-project__install-count-container");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			append(div, span);
    			append(span, t);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*project*/ 1 && t_value !== (t_value = window.Drupal.t('@count installs ', {
    				'@count': /*project*/ ctx[0].project_usage_total.toLocaleString()
    			}) + "")) set_data(t, t_value);
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    		}
    	};
    }

    // (68:4) {#if project.warnings && project.warnings.length > 0}
    function create_if_block_3$1(ctx) {
    	let each_1_anchor;
    	let each_value = /*project*/ ctx[0].warnings;
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$3(get_each_context$3(ctx, each_value, i));
    	}

    	return {
    		c() {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			each_1_anchor = empty();
    		},
    		m(target, anchor) {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(target, anchor);
    			}

    			insert(target, each_1_anchor, anchor);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*project, FULL_MODULE_PATH*/ 1) {
    				each_value = /*project*/ ctx[0].warnings;
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$3(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$3(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(each_1_anchor.parentNode, each_1_anchor);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}
    		},
    		d(detaching) {
    			destroy_each(each_blocks, detaching);
    			if (detaching) detach(each_1_anchor);
    		}
    	};
    }

    // (69:6) {#each project.warnings as warning}
    function create_each_block$3(ctx) {
    	let span;
    	let img;
    	let img_src_value;
    	let t0;
    	let small;
    	let raw_value = /*warning*/ ctx[8] + "";
    	let t1;

    	return {
    		c() {
    			span = element("span");
    			img = element("img");
    			t0 = space();
    			small = element("small");
    			t1 = space();
    			if (!src_url_equal(img.src, img_src_value = "" + (FULL_MODULE_PATH + "/images/triangle-alert.svg"))) attr(img, "src", img_src_value);
    			attr(img, "alt", "");
    			attr(span, "class", "pb-project__status-icon");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    			append(span, img);
    			append(span, t0);
    			append(span, small);
    			small.innerHTML = raw_value;
    			append(span, t1);
    		},
    		p(ctx, dirty) {
    			if (dirty & /*project*/ 1 && raw_value !== (raw_value = /*warning*/ ctx[8] + "")) small.innerHTML = raw_value;		},
    		d(detaching) {
    			if (detaching) detach(span);
    		}
    	};
    }

    // (76:4) {#if toggleView === 'List' && project.project_usage_total !== -1}
    function create_if_block_2$2(ctx) {
    	let div2;
    	let div0;
    	let projecticon;
    	let div0_class_value;
    	let t0;
    	let div1;
    	let t1_value = /*project*/ ctx[0].project_usage_total.toLocaleString() + "";
    	let t1;
    	let t2;
    	let current;

    	projecticon = new ProjectIcon({
    			props: {
    				type: "usage",
    				variant: "project-listing"
    			}
    		});

    	return {
    		c() {
    			div2 = element("div");
    			div0 = element("div");
    			create_component(projecticon.$$.fragment);
    			t0 = space();
    			div1 = element("div");
    			t1 = text(t1_value);
    			t2 = text(" Active Installs");
    			attr(div0, "class", div0_class_value = "pb-project__image pb-project__image--" + /*displayMode*/ ctx[2]);
    			attr(div1, "class", "pb-project__active-installs-text");
    			attr(div2, "class", "pb-project__project-usage-container");
    		},
    		m(target, anchor) {
    			insert(target, div2, anchor);
    			append(div2, div0);
    			mount_component(projecticon, div0, null);
    			append(div2, t0);
    			append(div2, div1);
    			append(div1, t1);
    			append(div1, t2);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if (!current || dirty & /*displayMode*/ 4 && div0_class_value !== (div0_class_value = "pb-project__image pb-project__image--" + /*displayMode*/ ctx[2])) {
    				attr(div0, "class", div0_class_value);
    			}

    			if ((!current || dirty & /*project*/ 1) && t1_value !== (t1_value = /*project*/ ctx[0].project_usage_total.toLocaleString() + "")) set_data(t1, t1_value);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projecticon.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projecticon.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div2);
    			destroy_component(projecticon);
    		}
    	};
    }

    // (88:4) {#if !project.warnings || project.warnings.length === 0}
    function create_if_block_1$5(ctx) {
    	let actionbutton;
    	let current;
    	actionbutton = new ActionButton({ props: { project: /*project*/ ctx[0] } });

    	return {
    		c() {
    			create_component(actionbutton.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(actionbutton, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const actionbutton_changes = {};
    			if (dirty & /*project*/ 1) actionbutton_changes.project = /*project*/ ctx[0];
    			actionbutton.$set(actionbutton_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(actionbutton.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(actionbutton.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(actionbutton, detaching);
    		}
    	};
    }

    // (94:2) {#if project.warnings && project.warnings.length > 0}
    function create_if_block$5(ctx) {
    	let actionbutton;
    	let current;
    	actionbutton = new ActionButton({ props: { project: /*project*/ ctx[0] } });

    	return {
    		c() {
    			create_component(actionbutton.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(actionbutton, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const actionbutton_changes = {};
    			if (dirty & /*project*/ 1) actionbutton_changes.project = /*project*/ ctx[0];
    			actionbutton.$set(actionbutton_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(actionbutton.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(actionbutton.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(actionbutton, detaching);
    		}
    	};
    }

    function create_fragment$5(ctx) {
    	let li;
    	let div0;
    	let image;
    	let div0_class_value;
    	let t0;
    	let div2;
    	let h3;
    	let a;
    	let t1_value = /*project*/ ctx[0].title + "";
    	let t1;
    	let a_id_value;
    	let a_href_value;
    	let h3_class_value;
    	let t2;
    	let div1;
    	let raw_value = /*project*/ ctx[0].body.summary + "";
    	let div1_class_value;
    	let t3;
    	let categories;
    	let div2_class_value;
    	let t4;
    	let div3;
    	let t5;
    	let t6;
    	let t7;
    	let t8;
    	let div3_class_value;
    	let t9;
    	let li_class_value;
    	let current;
    	let mounted;
    	let dispose;

    	image = new Image({
    			props: {
    				sources: /*project*/ ctx[0].logo,
    				class: "pb-project__logo-image"
    			}
    		});

    	categories = new Categories({
    			props: {
    				toggleView: /*toggleView*/ ctx[1],
    				moduleCategories: /*project*/ ctx[0].module_categories
    			}
    		});

    	let if_block0 = /*project*/ ctx[0].is_covered && create_if_block_5(ctx);
    	let if_block1 = /*toggleView*/ ctx[1] === 'Grid' && /*project*/ ctx[0].project_usage_total !== -1 && create_if_block_4$1(ctx);
    	let if_block2 = /*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0 && create_if_block_3$1(ctx);
    	let if_block3 = /*toggleView*/ ctx[1] === 'List' && /*project*/ ctx[0].project_usage_total !== -1 && create_if_block_2$2(ctx);
    	let if_block4 = (!/*project*/ ctx[0].warnings || /*project*/ ctx[0].warnings.length === 0) && create_if_block_1$5(ctx);
    	let if_block5 = /*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0 && create_if_block$5(ctx);

    	return {
    		c() {
    			li = element("li");
    			div0 = element("div");
    			create_component(image.$$.fragment);
    			t0 = space();
    			div2 = element("div");
    			h3 = element("h3");
    			a = element("a");
    			t1 = text(t1_value);
    			t2 = space();
    			div1 = element("div");
    			t3 = space();
    			create_component(categories.$$.fragment);
    			t4 = space();
    			div3 = element("div");
    			if (if_block0) if_block0.c();
    			t5 = space();
    			if (if_block1) if_block1.c();
    			t6 = space();
    			if (if_block2) if_block2.c();
    			t7 = space();
    			if (if_block3) if_block3.c();
    			t8 = space();
    			if (if_block4) if_block4.c();
    			t9 = space();
    			if (if_block5) if_block5.c();
    			attr(div0, "class", div0_class_value = "pb-project__logo pb-project__logo--" + /*displayMode*/ ctx[2]);
    			attr(a, "id", a_id_value = "" + (/*project*/ ctx[0].project_machine_name + "_title"));
    			attr(a, "class", "pb-project__link");
    			attr(a, "href", a_href_value = "" + (ORIGIN_URL + "/admin/modules/browse/" + /*project*/ ctx[0].project_machine_name));
    			attr(a, "rel", "noreferrer");
    			attr(h3, "class", h3_class_value = "pb-project__title pb-project__title--" + /*displayMode*/ ctx[2]);
    			attr(div1, "class", div1_class_value = "pb-project__body pb-project__body--" + /*displayMode*/ ctx[2]);
    			attr(div2, "class", div2_class_value = "pb-project__main pb-project__main--" + /*displayMode*/ ctx[2]);
    			attr(div3, "class", div3_class_value = "pb-project__icons pb-project__icons--" + /*displayMode*/ ctx[2]);
    			toggle_class(div3, "warnings", /*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0);
    			attr(li, "class", li_class_value = "pb-project pb-project--" + /*displayMode*/ ctx[2]);
    		},
    		m(target, anchor) {
    			insert(target, li, anchor);
    			append(li, div0);
    			mount_component(image, div0, null);
    			append(li, t0);
    			append(li, div2);
    			append(div2, h3);
    			append(h3, a);
    			append(a, t1);
    			append(div2, t2);
    			append(div2, div1);
    			div1.innerHTML = raw_value;
    			append(div2, t3);
    			mount_component(categories, div2, null);
    			append(li, t4);
    			append(li, div3);
    			if (if_block0) if_block0.m(div3, null);
    			append(div3, t5);
    			if (if_block1) if_block1.m(div3, null);
    			append(div3, t6);
    			if (if_block2) if_block2.m(div3, null);
    			append(div3, t7);
    			if (if_block3) if_block3.m(div3, null);
    			append(div3, t8);
    			if (if_block4) if_block4.m(div3, null);
    			append(li, t9);
    			if (if_block5) if_block5.m(li, null);
    			current = true;

    			if (!mounted) {
    				dispose = listen(h3, "click", /*click_handler*/ ctx[6]);
    				mounted = true;
    			}
    		},
    		p(ctx, [dirty]) {
    			const image_changes = {};
    			if (dirty & /*project*/ 1) image_changes.sources = /*project*/ ctx[0].logo;
    			image.$set(image_changes);

    			if (!current || dirty & /*displayMode*/ 4 && div0_class_value !== (div0_class_value = "pb-project__logo pb-project__logo--" + /*displayMode*/ ctx[2])) {
    				attr(div0, "class", div0_class_value);
    			}

    			if ((!current || dirty & /*project*/ 1) && t1_value !== (t1_value = /*project*/ ctx[0].title + "")) set_data(t1, t1_value);

    			if (!current || dirty & /*project*/ 1 && a_id_value !== (a_id_value = "" + (/*project*/ ctx[0].project_machine_name + "_title"))) {
    				attr(a, "id", a_id_value);
    			}

    			if (!current || dirty & /*project*/ 1 && a_href_value !== (a_href_value = "" + (ORIGIN_URL + "/admin/modules/browse/" + /*project*/ ctx[0].project_machine_name))) {
    				attr(a, "href", a_href_value);
    			}

    			if (!current || dirty & /*displayMode*/ 4 && h3_class_value !== (h3_class_value = "pb-project__title pb-project__title--" + /*displayMode*/ ctx[2])) {
    				attr(h3, "class", h3_class_value);
    			}

    			if ((!current || dirty & /*project*/ 1) && raw_value !== (raw_value = /*project*/ ctx[0].body.summary + "")) div1.innerHTML = raw_value;
    			if (!current || dirty & /*displayMode*/ 4 && div1_class_value !== (div1_class_value = "pb-project__body pb-project__body--" + /*displayMode*/ ctx[2])) {
    				attr(div1, "class", div1_class_value);
    			}

    			const categories_changes = {};
    			if (dirty & /*toggleView*/ 2) categories_changes.toggleView = /*toggleView*/ ctx[1];
    			if (dirty & /*project*/ 1) categories_changes.moduleCategories = /*project*/ ctx[0].module_categories;
    			categories.$set(categories_changes);

    			if (!current || dirty & /*displayMode*/ 4 && div2_class_value !== (div2_class_value = "pb-project__main pb-project__main--" + /*displayMode*/ ctx[2])) {
    				attr(div2, "class", div2_class_value);
    			}

    			if (/*project*/ ctx[0].is_covered) {
    				if (if_block0) {
    					if_block0.p(ctx, dirty);

    					if (dirty & /*project*/ 1) {
    						transition_in(if_block0, 1);
    					}
    				} else {
    					if_block0 = create_if_block_5(ctx);
    					if_block0.c();
    					transition_in(if_block0, 1);
    					if_block0.m(div3, t5);
    				}
    			} else if (if_block0) {
    				group_outros();

    				transition_out(if_block0, 1, 1, () => {
    					if_block0 = null;
    				});

    				check_outros();
    			}

    			if (/*toggleView*/ ctx[1] === 'Grid' && /*project*/ ctx[0].project_usage_total !== -1) {
    				if (if_block1) {
    					if_block1.p(ctx, dirty);
    				} else {
    					if_block1 = create_if_block_4$1(ctx);
    					if_block1.c();
    					if_block1.m(div3, t6);
    				}
    			} else if (if_block1) {
    				if_block1.d(1);
    				if_block1 = null;
    			}

    			if (/*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0) {
    				if (if_block2) {
    					if_block2.p(ctx, dirty);
    				} else {
    					if_block2 = create_if_block_3$1(ctx);
    					if_block2.c();
    					if_block2.m(div3, t7);
    				}
    			} else if (if_block2) {
    				if_block2.d(1);
    				if_block2 = null;
    			}

    			if (/*toggleView*/ ctx[1] === 'List' && /*project*/ ctx[0].project_usage_total !== -1) {
    				if (if_block3) {
    					if_block3.p(ctx, dirty);

    					if (dirty & /*toggleView, project*/ 3) {
    						transition_in(if_block3, 1);
    					}
    				} else {
    					if_block3 = create_if_block_2$2(ctx);
    					if_block3.c();
    					transition_in(if_block3, 1);
    					if_block3.m(div3, t8);
    				}
    			} else if (if_block3) {
    				group_outros();

    				transition_out(if_block3, 1, 1, () => {
    					if_block3 = null;
    				});

    				check_outros();
    			}

    			if (!/*project*/ ctx[0].warnings || /*project*/ ctx[0].warnings.length === 0) {
    				if (if_block4) {
    					if_block4.p(ctx, dirty);

    					if (dirty & /*project*/ 1) {
    						transition_in(if_block4, 1);
    					}
    				} else {
    					if_block4 = create_if_block_1$5(ctx);
    					if_block4.c();
    					transition_in(if_block4, 1);
    					if_block4.m(div3, null);
    				}
    			} else if (if_block4) {
    				group_outros();

    				transition_out(if_block4, 1, 1, () => {
    					if_block4 = null;
    				});

    				check_outros();
    			}

    			if (!current || dirty & /*displayMode*/ 4 && div3_class_value !== (div3_class_value = "pb-project__icons pb-project__icons--" + /*displayMode*/ ctx[2])) {
    				attr(div3, "class", div3_class_value);
    			}

    			if (dirty & /*displayMode, project*/ 5) {
    				toggle_class(div3, "warnings", /*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0);
    			}

    			if (/*project*/ ctx[0].warnings && /*project*/ ctx[0].warnings.length > 0) {
    				if (if_block5) {
    					if_block5.p(ctx, dirty);

    					if (dirty & /*project*/ 1) {
    						transition_in(if_block5, 1);
    					}
    				} else {
    					if_block5 = create_if_block$5(ctx);
    					if_block5.c();
    					transition_in(if_block5, 1);
    					if_block5.m(li, null);
    				}
    			} else if (if_block5) {
    				group_outros();

    				transition_out(if_block5, 1, 1, () => {
    					if_block5 = null;
    				});

    				check_outros();
    			}

    			if (!current || dirty & /*displayMode*/ 4 && li_class_value !== (li_class_value = "pb-project pb-project--" + /*displayMode*/ ctx[2])) {
    				attr(li, "class", li_class_value);
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(image.$$.fragment, local);
    			transition_in(categories.$$.fragment, local);
    			transition_in(if_block0);
    			transition_in(if_block3);
    			transition_in(if_block4);
    			transition_in(if_block5);
    			current = true;
    		},
    		o(local) {
    			transition_out(image.$$.fragment, local);
    			transition_out(categories.$$.fragment, local);
    			transition_out(if_block0);
    			transition_out(if_block3);
    			transition_out(if_block4);
    			transition_out(if_block5);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(li);
    			destroy_component(image);
    			destroy_component(categories);
    			if (if_block0) if_block0.d();
    			if (if_block1) if_block1.d();
    			if (if_block2) if_block2.d();
    			if (if_block3) if_block3.d();
    			if (if_block4) if_block4.d();
    			if (if_block5) if_block5.d();
    			mounted = false;
    			dispose();
    		}
    	};
    }

    function instance$5($$self, $$props, $$invalidate) {
    	let isDesktop;
    	let displayMode;
    	let $focusedElement;
    	component_subscribe($$self, focusedElement, $$value => $$invalidate(3, $focusedElement = $$value));
    	let { project } = $$props;
    	let { toggleView } = $$props;
    	let mqMatches;

    	mediaQueryValues.subscribe(mqlMap => {
    		$$invalidate(4, mqMatches = mqlMap.get('(min-width: 1200px)'));
    	});

    	const click_handler = () => {
    		set_store_value(focusedElement, $focusedElement = `${project.project_machine_name}_title`, $focusedElement);
    	};

    	$$self.$$set = $$props => {
    		if ('project' in $$props) $$invalidate(0, project = $$props.project);
    		if ('toggleView' in $$props) $$invalidate(1, toggleView = $$props.toggleView);
    	};

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*mqMatches*/ 16) {
    			$$invalidate(5, isDesktop = mqMatches);
    		}

    		if ($$self.$$.dirty & /*isDesktop, toggleView*/ 34) {
    			$$invalidate(2, displayMode = isDesktop ? toggleView.toLowerCase() : 'list');
    		}
    	};

    	return [
    		project,
    		toggleView,
    		displayMode,
    		$focusedElement,
    		mqMatches,
    		isDesktop,
    		click_handler
    	];
    }

    class Project extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$5, create_fragment$5, safe_not_equal, { project: 0, toggleView: 1 });
    	}
    }

    /* src/Tabs.svelte generated by Svelte v3.48.0 */

    function get_each_context$2(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[9] = list[i].pluginId;
    	child_ctx[10] = list[i].pluginLabel;
    	child_ctx[11] = list[i].totalResults;
    	child_ctx[12] = list[i].isActive;
    	return child_ctx;
    }

    // (47:0) {#if dataArray.length >= 2}
    function create_if_block$4(ctx) {
    	let nav;
    	let div;
    	let div_aria_label_value;
    	let mounted;
    	let dispose;
    	let each_value = /*dataArray*/ ctx[0].map(/*func*/ ctx[6]);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$2(get_each_context$2(ctx, each_value, i));
    	}

    	return {
    		c() {
    			nav = element("nav");
    			div = element("div");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			attr(div, "role", "tablist");
    			attr(div, "id", "plugin-tabs");
    			attr(div, "aria-label", div_aria_label_value = window.Drupal.t('Plugin tabs'));
    			attr(div, "class", "tabs tabs--secondary pb-tabs");
    			attr(nav, "class", "tabs-wrapper tabs-wrapper--secondary is-horizontal");
    		},
    		m(target, anchor) {
    			insert(target, nav, anchor);
    			append(nav, div);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(div, null);
    			}

    			/*div_binding*/ ctx[8](div);

    			if (!mounted) {
    				dispose = listen(div, "keydown", /*onKeydown*/ ctx[5]);
    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			if (dirty & /*dataArray, $activeTab, dispatch, window, Drupal*/ 29) {
    				each_value = /*dataArray*/ ctx[0].map(/*func*/ ctx[6]);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$2(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$2(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(div, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(nav);
    			destroy_each(each_blocks, detaching);
    			/*div_binding*/ ctx[8](null);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (84:12) {#if isActive}
    function create_if_block_1$4(ctx) {
    	let span;

    	return {
    		c() {
    			span = element("span");
    			span.textContent = `(${window.Drupal.t('active tab')})`;
    			attr(span, "class", "visually-hidden");
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    		},
    		p: noop,
    		d(detaching) {
    			if (detaching) detach(span);
    		}
    	};
    }

    // (57:6) {#each dataArray.map( (item) => ({ ...item, isActive: item.pluginId === $activeTab }), ) as { pluginId, pluginLabel, totalResults, isActive }}
    function create_each_block$2(ctx) {
    	let span;
    	let button;
    	let t0_value = /*pluginLabel*/ ctx[10] + "";
    	let t0;
    	let t1;
    	let br;
    	let t2;
    	let t3_value = /*Drupal*/ ctx[3].formatPlural(/*totalResults*/ ctx[11], '1 result', '@count results') + "";
    	let t3;
    	let t4;
    	let button_aria_selected_value;
    	let button_aria_controls_value;
    	let button_tabindex_value;
    	let button_id_value;
    	let button_value_value;
    	let t5;
    	let mounted;
    	let dispose;
    	let if_block = /*isActive*/ ctx[12] && create_if_block_1$4();

    	function click_handler(...args) {
    		return /*click_handler*/ ctx[7](/*pluginId*/ ctx[9], ...args);
    	}

    	return {
    		c() {
    			span = element("span");
    			button = element("button");
    			t0 = text(t0_value);
    			t1 = space();
    			br = element("br");
    			t2 = space();
    			t3 = text(t3_value);
    			t4 = space();
    			if (if_block) if_block.c();
    			t5 = space();
    			attr(button, "type", "button");
    			attr(button, "role", "tab");
    			attr(button, "aria-selected", button_aria_selected_value = /*isActive*/ ctx[12] ? 'true' : 'false');
    			attr(button, "aria-controls", button_aria_controls_value = /*pluginId*/ ctx[9]);
    			attr(button, "tabindex", button_tabindex_value = /*isActive*/ ctx[12] ? '0' : '-1');
    			attr(button, "id", button_id_value = /*pluginId*/ ctx[9]);
    			attr(button, "class", "pb-tabs__link tabs__link");
    			button.value = button_value_value = /*pluginId*/ ctx[9];
    			toggle_class(button, "is-active", /*isActive*/ ctx[12] === true);
    			toggle_class(button, "pb-tabs__link--active", /*isActive*/ ctx[12] === true);
    			attr(span, "class", "tabs__tab pb-tabs__tab");
    			toggle_class(span, "is-active", /*isActive*/ ctx[12] === true);
    			toggle_class(span, "pb-tabs__tab--active", /*isActive*/ ctx[12] === true);
    		},
    		m(target, anchor) {
    			insert(target, span, anchor);
    			append(span, button);
    			append(button, t0);
    			append(button, t1);
    			append(button, br);
    			append(button, t2);
    			append(button, t3);
    			append(button, t4);
    			if (if_block) if_block.m(button, null);
    			append(span, t5);

    			if (!mounted) {
    				dispose = listen(button, "click", click_handler);
    				mounted = true;
    			}
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			if (dirty & /*dataArray, $activeTab*/ 5 && t0_value !== (t0_value = /*pluginLabel*/ ctx[10] + "")) set_data(t0, t0_value);
    			if (dirty & /*dataArray, $activeTab*/ 5 && t3_value !== (t3_value = /*Drupal*/ ctx[3].formatPlural(/*totalResults*/ ctx[11], '1 result', '@count results') + "")) set_data(t3, t3_value);

    			if (/*isActive*/ ctx[12]) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block_1$4();
    					if_block.c();
    					if_block.m(button, null);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5 && button_aria_selected_value !== (button_aria_selected_value = /*isActive*/ ctx[12] ? 'true' : 'false')) {
    				attr(button, "aria-selected", button_aria_selected_value);
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5 && button_aria_controls_value !== (button_aria_controls_value = /*pluginId*/ ctx[9])) {
    				attr(button, "aria-controls", button_aria_controls_value);
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5 && button_tabindex_value !== (button_tabindex_value = /*isActive*/ ctx[12] ? '0' : '-1')) {
    				attr(button, "tabindex", button_tabindex_value);
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5 && button_id_value !== (button_id_value = /*pluginId*/ ctx[9])) {
    				attr(button, "id", button_id_value);
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5 && button_value_value !== (button_value_value = /*pluginId*/ ctx[9])) {
    				button.value = button_value_value;
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5) {
    				toggle_class(button, "is-active", /*isActive*/ ctx[12] === true);
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5) {
    				toggle_class(button, "pb-tabs__link--active", /*isActive*/ ctx[12] === true);
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5) {
    				toggle_class(span, "is-active", /*isActive*/ ctx[12] === true);
    			}

    			if (dirty & /*dataArray, $activeTab*/ 5) {
    				toggle_class(span, "pb-tabs__tab--active", /*isActive*/ ctx[12] === true);
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(span);
    			if (if_block) if_block.d();
    			mounted = false;
    			dispose();
    		}
    	};
    }

    function create_fragment$4(ctx) {
    	let if_block_anchor;
    	let if_block = /*dataArray*/ ctx[0].length >= 2 && create_if_block$4(ctx);

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    		},
    		p(ctx, [dirty]) {
    			if (/*dataArray*/ ctx[0].length >= 2) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block$4(ctx);
    					if_block.c();
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		i: noop,
    		o: noop,
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    function instance$4($$self, $$props, $$invalidate) {
    	let $activeTab;
    	component_subscribe($$self, activeTab, $$value => $$invalidate(2, $activeTab = $$value));
    	const { Drupal } = window;
    	const dispatch = createEventDispatcher();
    	let { dataArray = [] } = $$props;
    	let tabButtons;

    	// Enable arrow navigation between tabs in the tab list
    	function onKeydown(e) {
    		// Enable arrow navigation between tabs in the tab list
    		let tabFocus;

    		const tabs = tabButtons.querySelectorAll('[role="tab"]');

    		for (let i = 0; i < tabs.length; i++) {
    			if (tabs[i].getAttribute('tabindex') === '0') {
    				tabFocus = i;
    			}
    		}

    		// Move right
    		if (e.keyCode === 39 || e.keyCode === 37) {
    			tabs[tabFocus].setAttribute('tabindex', -1);

    			if (e.keyCode === 39) {
    				tabFocus += 1;

    				// If we're at the end, go to the start
    				if (tabFocus >= tabs.length) {
    					tabFocus = 0;
    				}
    			} else if (e.keyCode === 37) {
    				tabFocus -= 1; // Move left

    				// If we're at the start, move to the end
    				if (tabFocus < 0) {
    					tabFocus = tabs.length - 1;
    				}
    			}

    			tabs[tabFocus].setAttribute('tabindex', 0);
    			tabs[tabFocus].focus();
    		}
    	}

    	const func = item => ({
    		...item,
    		isActive: item.pluginId === $activeTab
    	});

    	const click_handler = (pluginId, event) => {
    		dispatch('tabChange', { pluginId, event });
    	};

    	function div_binding($$value) {
    		binding_callbacks[$$value ? 'unshift' : 'push'](() => {
    			tabButtons = $$value;
    			$$invalidate(1, tabButtons);
    		});
    	}

    	$$self.$$set = $$props => {
    		if ('dataArray' in $$props) $$invalidate(0, dataArray = $$props.dataArray);
    	};

    	return [
    		dataArray,
    		tabButtons,
    		$activeTab,
    		Drupal,
    		dispatch,
    		onKeydown,
    		func,
    		click_handler,
    		div_binding
    	];
    }

    class Tabs extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$4, create_fragment$4, safe_not_equal, { dataArray: 0 });
    	}
    }

    /* src/ProjectBrowser.svelte generated by Svelte v3.48.0 */

    function get_each_context_1(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[49] = list[i];
    	child_ctx[51] = i;
    	return child_ctx;
    }

    function get_each_context$1(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[46] = list[i];
    	return child_ctx;
    }

    // (386:4) {#each rows as row, index (row)}
    function create_each_block_1(key_1, ctx) {
    	let first;
    	let project;
    	let current;

    	project = new Project({
    			props: {
    				toggleView: /*toggleView*/ ctx[4],
    				project: /*row*/ ctx[49]
    			}
    		});

    	return {
    		key: key_1,
    		first: null,
    		c() {
    			first = empty();
    			create_component(project.$$.fragment);
    			this.first = first;
    		},
    		m(target, anchor) {
    			insert(target, first, anchor);
    			mount_component(project, target, anchor);
    			current = true;
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			const project_changes = {};
    			if (dirty[0] & /*toggleView*/ 16) project_changes.toggleView = /*toggleView*/ ctx[4];
    			if (dirty[0] & /*rows*/ 1024) project_changes.project = /*row*/ ctx[49];
    			project.$set(project_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(project.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(project.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(first);
    			destroy_component(project, detaching);
    		}
    	};
    }

    // (318:2) <ProjectGrid {toggleView} {loading} {rows} {pageIndex} {$pageSize} let:rows>
    function create_default_slot_1(ctx) {
    	let each_blocks = [];
    	let each_1_lookup = new Map();
    	let each_1_anchor;
    	let current;
    	let each_value_1 = /*rows*/ ctx[10];
    	const get_key = ctx => /*row*/ ctx[49];

    	for (let i = 0; i < each_value_1.length; i += 1) {
    		let child_ctx = get_each_context_1(ctx, each_value_1, i);
    		let key = get_key(child_ctx);
    		each_1_lookup.set(key, each_blocks[i] = create_each_block_1(key, child_ctx));
    	}

    	return {
    		c() {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			each_1_anchor = empty();
    		},
    		m(target, anchor) {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(target, anchor);
    			}

    			insert(target, each_1_anchor, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if (dirty[0] & /*toggleView, rows*/ 1040) {
    				each_value_1 = /*rows*/ ctx[10];
    				group_outros();
    				each_blocks = update_keyed_each(each_blocks, dirty, get_key, 1, ctx, each_value_1, each_1_lookup, each_1_anchor.parentNode, outro_and_destroy_block, create_each_block_1, each_1_anchor, get_each_context_1);
    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;

    			for (let i = 0; i < each_value_1.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			current = true;
    		},
    		o(local) {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			current = false;
    		},
    		d(detaching) {
    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].d(detaching);
    			}

    			if (detaching) detach(each_1_anchor);
    		}
    	};
    }

    // (334:12) {#if $activeTab === dataValue.pluginId}
    function create_if_block_1$3(ctx) {
    	let t0_value = (/*$rowsCount*/ ctx[8] && numberFormatter.format(/*$rowsCount*/ ctx[8])) + "";
    	let t0;
    	let t1;
    	let t2_value = window.Drupal.t('Results') + "";
    	let t2;

    	return {
    		c() {
    			t0 = text(t0_value);
    			t1 = space();
    			t2 = text(t2_value);
    		},
    		m(target, anchor) {
    			insert(target, t0, anchor);
    			insert(target, t1, anchor);
    			insert(target, t2, anchor);
    		},
    		p(ctx, dirty) {
    			if (dirty[0] & /*$rowsCount*/ 256 && t0_value !== (t0_value = (/*$rowsCount*/ ctx[8] && numberFormatter.format(/*$rowsCount*/ ctx[8])) + "")) set_data(t0, t0_value);
    		},
    		d(detaching) {
    			if (detaching) detach(t0);
    			if (detaching) detach(t1);
    			if (detaching) detach(t2);
    		}
    	};
    }

    // (333:10) {#each dataArray as dataValue}
    function create_each_block$1(ctx) {
    	let if_block_anchor;
    	let if_block = /*$activeTab*/ ctx[7] === /*dataValue*/ ctx[46].pluginId && create_if_block_1$3(ctx);

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    		},
    		p(ctx, dirty) {
    			if (/*$activeTab*/ ctx[7] === /*dataValue*/ ctx[46].pluginId) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block_1$3(ctx);
    					if_block.c();
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (341:8) {#if matches}
    function create_if_block$3(ctx) {
    	let div;
    	let button0;
    	let img0;
    	let img0_src_value;
    	let t0;
    	let t1_value = window.Drupal.t('List') + "";
    	let t1;
    	let t2;
    	let button1;
    	let img1;
    	let img1_src_value;
    	let t3;
    	let t4_value = window.Drupal.t('Grid') + "";
    	let t4;
    	let mounted;
    	let dispose;

    	return {
    		c() {
    			div = element("div");
    			button0 = element("button");
    			img0 = element("img");
    			t0 = space();
    			t1 = text(t1_value);
    			t2 = space();
    			button1 = element("button");
    			img1 = element("img");
    			t3 = space();
    			t4 = text(t4_value);
    			attr(img0, "class", "pb-display__button-icon project-browser__list-icon");
    			if (!src_url_equal(img0.src, img0_src_value = "" + (FULL_MODULE_PATH + "/images/list.svg"))) attr(img0, "src", img0_src_value);
    			attr(img0, "alt", "");
    			attr(button0, "class", "pb-display__button pb-display__button--first");
    			button0.value = "List";
    			toggle_class(button0, "pb-display__button--selected", /*toggleView*/ ctx[4] === 'List');
    			attr(img1, "class", "pb-display__button-icon project-browser__grid-icon");
    			if (!src_url_equal(img1.src, img1_src_value = "" + (FULL_MODULE_PATH + "/images/grid-fill.svg"))) attr(img1, "src", img1_src_value);
    			attr(img1, "alt", "");
    			attr(button1, "class", "pb-display__button pb-display__button--last");
    			button1.value = "Grid";
    			toggle_class(button1, "pb-display__button--selected", /*toggleView*/ ctx[4] === 'Grid');
    			attr(div, "class", "pb-display");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			append(div, button0);
    			append(button0, img0);
    			append(button0, t0);
    			append(button0, t1);
    			append(div, t2);
    			append(div, button1);
    			append(button1, img1);
    			append(button1, t3);
    			append(button1, t4);

    			if (!mounted) {
    				dispose = [
    					listen(button0, "click", /*click_handler*/ ctx[24]),
    					listen(button1, "click", /*click_handler_1*/ ctx[25])
    				];

    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			if (dirty[0] & /*toggleView*/ 16) {
    				toggle_class(button0, "pb-display__button--selected", /*toggleView*/ ctx[4] === 'List');
    			}

    			if (dirty[0] & /*toggleView*/ 16) {
    				toggle_class(button1, "pb-display__button--selected", /*toggleView*/ ctx[4] === 'Grid');
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			mounted = false;
    			run_all(dispose);
    		}
    	};
    }

    // (319:4) 
    function create_head_slot(ctx) {
    	let div2;
    	let tabs;
    	let t0;
    	let search;
    	let t1;
    	let div1;
    	let div0;
    	let t2;
    	let current;

    	tabs = new Tabs({
    			props: { dataArray: /*dataArray*/ ctx[2] }
    		});

    	tabs.$on("tabChange", /*toggleRows*/ ctx[20]);

    	let search_props = {
    		searchText: /*searchText*/ ctx[0],
    		refreshLiveRegion: /*refreshLiveRegion*/ ctx[21]
    	};

    	search = new Search({ props: search_props });
    	/*search_binding*/ ctx[23](search);
    	search.$on("search", /*onSearch*/ ctx[15]);
    	search.$on("sort", /*onSort*/ ctx[17]);
    	search.$on("advancedFilter", /*onAdvancedFilter*/ ctx[18]);
    	search.$on("selectCategory", /*onSelectCategory*/ ctx[16]);
    	let each_value = /*dataArray*/ ctx[2];
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));
    	}

    	let if_block = /*matches*/ ctx[45] && create_if_block$3(ctx);

    	return {
    		c() {
    			div2 = element("div");
    			create_component(tabs.$$.fragment);
    			t0 = space();
    			create_component(search.$$.fragment);
    			t1 = space();
    			div1 = element("div");
    			div0 = element("div");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t2 = space();
    			if (if_block) if_block.c();
    			attr(div0, "class", "pb-search-results");
    			attr(div1, "class", "pb-layout__header");
    			attr(div2, "slot", "head");
    		},
    		m(target, anchor) {
    			insert(target, div2, anchor);
    			mount_component(tabs, div2, null);
    			append(div2, t0);
    			mount_component(search, div2, null);
    			append(div2, t1);
    			append(div2, div1);
    			append(div1, div0);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(div0, null);
    			}

    			append(div1, t2);
    			if (if_block) if_block.m(div1, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const tabs_changes = {};
    			if (dirty[0] & /*dataArray*/ 4) tabs_changes.dataArray = /*dataArray*/ ctx[2];
    			tabs.$set(tabs_changes);
    			const search_changes = {};
    			if (dirty[0] & /*searchText*/ 1) search_changes.searchText = /*searchText*/ ctx[0];
    			search.$set(search_changes);

    			if (dirty[0] & /*$rowsCount, $activeTab, dataArray*/ 388) {
    				each_value = /*dataArray*/ ctx[2];
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$1(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$1(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(div0, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}

    			if (/*matches*/ ctx[45]) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block$3(ctx);
    					if_block.c();
    					if_block.m(div1, null);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(tabs.$$.fragment, local);
    			transition_in(search.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(tabs.$$.fragment, local);
    			transition_out(search.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div2);
    			destroy_component(tabs);
    			/*search_binding*/ ctx[23](null);
    			destroy_component(search);
    			destroy_each(each_blocks, detaching);
    			if (if_block) if_block.d();
    		}
    	};
    }

    // (380:4) 
    function create_left_slot(ctx) {
    	let div;
    	let filter;
    	let current;
    	let filter_props = {};
    	filter = new Filter({ props: filter_props });
    	/*filter_binding*/ ctx[22](filter);
    	filter.$on("selectCategory", /*onSelectCategory*/ ctx[16]);

    	return {
    		c() {
    			div = element("div");
    			create_component(filter.$$.fragment);
    			attr(div, "slot", "left");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			mount_component(filter, div, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const filter_changes = {};
    			filter.$set(filter_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(filter.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(filter.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			/*filter_binding*/ ctx[22](null);
    			destroy_component(filter);
    		}
    	};
    }

    // (389:4) 
    function create_bottom_slot(ctx) {
    	let div;
    	let pagination;
    	let current;

    	pagination = new Pagination({
    			props: {
    				page: /*$page*/ ctx[1],
    				count: /*$rowsCount*/ ctx[8]
    			}
    		});

    	pagination.$on("pageChange", /*onPageChange*/ ctx[13]);
    	pagination.$on("pageSizeChange", /*onPageSizeChange*/ ctx[14]);

    	return {
    		c() {
    			div = element("div");
    			create_component(pagination.$$.fragment);
    			attr(div, "slot", "bottom");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			mount_component(pagination, div, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const pagination_changes = {};
    			if (dirty[0] & /*$page*/ 2) pagination_changes.page = /*$page*/ ctx[1];
    			if (dirty[0] & /*$rowsCount*/ 256) pagination_changes.count = /*$rowsCount*/ ctx[8];
    			pagination.$set(pagination_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(pagination.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(pagination.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			destroy_component(pagination);
    		}
    	};
    }

    // (317:0) <MediaQuery query="(min-width: 1200px)" let:matches>
    function create_default_slot(ctx) {
    	let projectgrid;
    	let current;

    	projectgrid = new ProjectGrid({
    			props: {
    				toggleView: /*toggleView*/ ctx[4],
    				loading: /*loading*/ ctx[3],
    				rows: /*rows*/ ctx[10],
    				pageIndex,
    				$pageSize: /*$pageSize*/ ctx[9],
    				$$slots: {
    					bottom: [
    						create_bottom_slot,
    						({ rows }) => ({ 10: rows }),
    						({ rows }) => [rows ? 1024 : 0]
    					],
    					left: [
    						create_left_slot,
    						({ rows }) => ({ 10: rows }),
    						({ rows }) => [rows ? 1024 : 0]
    					],
    					head: [
    						create_head_slot,
    						({ rows }) => ({ 10: rows }),
    						({ rows }) => [rows ? 1024 : 0]
    					],
    					default: [
    						create_default_slot_1,
    						({ rows }) => ({ 10: rows }),
    						({ rows }) => [rows ? 1024 : 0]
    					]
    				},
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			create_component(projectgrid.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(projectgrid, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const projectgrid_changes = {};
    			if (dirty[0] & /*toggleView*/ 16) projectgrid_changes.toggleView = /*toggleView*/ ctx[4];
    			if (dirty[0] & /*loading*/ 8) projectgrid_changes.loading = /*loading*/ ctx[3];
    			if (dirty[0] & /*rows*/ 1024) projectgrid_changes.rows = /*rows*/ ctx[10];
    			if (dirty[0] & /*$pageSize*/ 512) projectgrid_changes.$pageSize = /*$pageSize*/ ctx[9];

    			if (dirty[0] & /*$page, $rowsCount, filterComponent, toggleView, dataArray, $activeTab, searchText, searchComponent, rows*/ 1527 | dirty[1] & /*$$scope, matches*/ 2113536) {
    				projectgrid_changes.$$scope = { dirty, ctx };
    			}

    			projectgrid.$set(projectgrid_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projectgrid.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectgrid.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projectgrid, detaching);
    		}
    	};
    }

    function create_fragment$3(ctx) {
    	let mediaquery;
    	let current;

    	mediaquery = new MediaQuery({
    			props: {
    				query: "(min-width: 1200px)",
    				$$slots: {
    					default: [
    						create_default_slot,
    						({ matches }) => ({ 45: matches }),
    						({ matches }) => [0, matches ? 16384 : 0]
    					]
    				},
    				$$scope: { ctx }
    			}
    		});

    	return {
    		c() {
    			create_component(mediaquery.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(mediaquery, target, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const mediaquery_changes = {};

    			if (dirty[0] & /*toggleView, loading, rows, $pageSize, $page, $rowsCount, filterComponent, dataArray, $activeTab, searchText, searchComponent*/ 2047 | dirty[1] & /*$$scope, matches*/ 2113536) {
    				mediaquery_changes.$$scope = { dirty, ctx };
    			}

    			mediaquery.$set(mediaquery_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(mediaquery.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(mediaquery.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(mediaquery, detaching);
    		}
    	};
    }

    const pageIndex = 0; // first row

    function instance$3($$self, $$props, $$invalidate) {
    	let $page;
    	let $previousPage;
    	let $activeTab;
    	let $rowsCount;
    	let $sortCriteria;
    	let $sort;
    	let $categoryCheckedTrack;
    	let $moduleCategoryFilter;
    	let $filters;
    	let $focusedElement;
    	let $isFirstLoad;
    	let $isPackageManagerRequired;
    	let $pageSize;
    	let $currentPage;
    	component_subscribe($$self, page, $$value => $$invalidate(1, $page = $$value));
    	component_subscribe($$self, activeTab, $$value => $$invalidate(7, $activeTab = $$value));
    	component_subscribe($$self, rowsCount, $$value => $$invalidate(8, $rowsCount = $$value));
    	component_subscribe($$self, sortCriteria, $$value => $$invalidate(31, $sortCriteria = $$value));
    	component_subscribe($$self, sort, $$value => $$invalidate(32, $sort = $$value));
    	component_subscribe($$self, categoryCheckedTrack, $$value => $$invalidate(33, $categoryCheckedTrack = $$value));
    	component_subscribe($$self, moduleCategoryFilter, $$value => $$invalidate(34, $moduleCategoryFilter = $$value));
    	component_subscribe($$self, filters, $$value => $$invalidate(35, $filters = $$value));
    	component_subscribe($$self, focusedElement, $$value => $$invalidate(36, $focusedElement = $$value));
    	component_subscribe($$self, isFirstLoad, $$value => $$invalidate(37, $isFirstLoad = $$value));
    	component_subscribe($$self, isPackageManagerRequired, $$value => $$invalidate(38, $isPackageManagerRequired = $$value));
    	component_subscribe($$self, pageSize, $$value => $$invalidate(9, $pageSize = $$value));
    	const { Drupal } = window;
    	const { announce } = Drupal;
    	let data;
    	let rows = [];
    	let dataArray = [];
    	let loading = true;
    	let sortText = $sortCriteria.find(option => option.id === $sort).text;
    	let { searchText } = $$props;

    	searchString.subscribe(value => {
    		$$invalidate(0, searchText = value);
    	});

    	let toggleView = 'Grid';

    	preferredView.subscribe(value => {
    		$$invalidate(4, toggleView = value);
    	});

    	const [currentPage, previousPage] = withPrevious(0);
    	component_subscribe($$self, currentPage, value => $$invalidate(39, $currentPage = value));
    	component_subscribe($$self, previousPage, value => $$invalidate(30, $previousPage = value));
    	let element = '';

    	focusedElement.subscribe(value => {
    		element = value;
    	});

    	let filterComponent;
    	let searchComponent;

    	/**
     * Load data from Drupal.org API.
     *
     * @param {number|string} _page
     *   The page number.
     *
     * @return {Promise<void>}
     *   Empty promise that resolves on content load.*
     */
    	async function load(_page) {
    		$$invalidate(3, loading = true);

    		const searchParams = new URLSearchParams({
    				page: _page,
    				limit: $pageSize,
    				sort: $sort,
    				source: $activeTab
    			});

    		if (searchText) {
    			searchParams.set('search', searchText);
    		}

    		if ($moduleCategoryFilter && $moduleCategoryFilter.length) {
    			searchParams.set('categories', $moduleCategoryFilter);
    		}

    		if ($filters.developmentStatus && $filters.developmentStatus.length) {
    			searchParams.set('development_status', $filters.developmentStatus);
    		}

    		if ($filters.maintenanceStatus && $filters.maintenanceStatus.length) {
    			searchParams.set('maintenance_status', $filters.maintenanceStatus);
    		}

    		if ($filters.securityCoverage && $filters.securityCoverage.length) {
    			searchParams.set('security_advisory_coverage', $filters.securityCoverage);
    		}

    		if (Object.keys($categoryCheckedTrack).length !== 0) {
    			searchParams.set('tabwise_categories', JSON.stringify($categoryCheckedTrack));
    		}

    		const url = `${ORIGIN_URL}/drupal-org-proxy/project?${searchParams.toString()}`;
    		const res = await fetch(url);

    		if (res.ok) {
    			data = await res.json();

    			$$invalidate(2, dataArray = Object.values(data));
    			$$invalidate(10, rows = data[$activeTab].list);
    			set_store_value(rowsCount, $rowsCount = data[$activeTab].totalResults, $rowsCount);
    			set_store_value(isPackageManagerRequired, $isPackageManagerRequired = data[$activeTab].isPackageManagerRequired, $isPackageManagerRequired);

    			if ($isPackageManagerRequired && PM_VALIDATION_ERROR && typeof PM_VALIDATION_ERROR === 'string' && MODULE_STATUS.package_manager && ALLOW_UI_INSTALL) {
    				const messenger = new Drupal.Message();
    				messenger.add(window.Drupal.t('Unable to download modules via the UI: !error', { '!error': PM_VALIDATION_ERROR }), { type: 'error' });
    			}
    		} else {
    			$$invalidate(10, rows = []);
    			set_store_value(rowsCount, $rowsCount = 0, $rowsCount);
    		}

    		$$invalidate(3, loading = false);
    	}

    	async function filterRecommended() {
    		// Show recommended projects on initial page load only when no filters are applied.
    		if ($filters.developmentStatus.length === 0 && $filters.maintenanceStatus.length === 0 && $filters.securityCoverage.length === 0) {
    			set_store_value(filters, $filters.maintenanceStatus = ACTIVELY_MAINTAINED_ID, $filters);
    			set_store_value(filters, $filters.securityCoverage = COVERED_ID, $filters);
    			set_store_value(filters, $filters.developmentStatus = ALL_VALUES_ID, $filters);
    		}

    		isFirstLoad.set(false);
    	}

    	/**
     * Load remote data when the Svelte component is mounted.
     */
    	onMount(async () => {
    		// If current active plugin is disabled, remove storage keys and reload page.
    		const settingsActiveTab = JSON.stringify(DEFAULT_SOURCE_ID);

    		if ($activeTab !== settingsActiveTab && CURRENT_SOURCES_KEYS.indexOf($activeTab) === -1) {
    			sessionStorage.removeItem('activeTab');
    			sessionStorage.removeItem('categoryFilter');
    			sessionStorage.removeItem('categoryCheckedTrack');
    			sessionStorage.setItem('activeTab', settingsActiveTab);
    			window.location.reload();
    		}

    		// Only filter by recommended on first page load.
    		if ($isFirstLoad) {
    			await filterRecommended();
    		}

    		await load($page);
    		const focus = element ? document.getElementById(element) : false;

    		if (focus) {
    			focus.focus();
    			set_store_value(focusedElement, $focusedElement = '', $focusedElement);
    		}
    	});

    	function onPageChange(event) {
    		const activePages = document.querySelectorAll(`[aria-label="Page ${$page + 1}"]`);

    		if (activePages) {
    			const activePage = activePages[0];
    			activePage.focus();
    		}

    		page.set(event.detail.page);
    		load($page);
    	}

    	function onPageSizeChange() {
    		page.set(0);
    		load($page);
    	}

    	async function onSearch(event) {
    		$$invalidate(0, searchText = event.detail.searchText);
    		await load(0);
    		page.set(0);
    	}

    	async function onSelectCategory(event) {
    		moduleCategoryFilter.set(event.detail.category);
    		await load(0);
    		page.set(0);
    	}

    	async function onSort(event) {
    		sort.set(event.detail.sort);
    		sortText = $sortCriteria.find(option => option.id === $sort).text;
    		await load(0);
    		page.set(0);
    	}

    	async function onAdvancedFilter(event) {
    		set_store_value(filters, $filters.developmentStatus = event.detail.developmentStatus, $filters);
    		set_store_value(filters, $filters.maintenanceStatus = event.detail.maintenanceStatus, $filters);
    		set_store_value(filters, $filters.securityCoverage = event.detail.securityCoverage, $filters);
    		await load(0);
    		page.set(0);
    	}

    	async function onToggle(val) {
    		if (val !== toggleView) $$invalidate(4, toggleView = val);
    		preferredView.set(val);
    	}

    	async function toggleRows(event) {
    		searchComponent.onSearch(event);
    		const { target } = event.detail.event;
    		const parent = target.parentNode;

    		// Remove all current selected tabs
    		parent.querySelectorAll('[aria-selected="true"]').forEach(t => t.setAttribute('aria-selected', false));

    		// Set this tab as selected
    		target.setAttribute('aria-selected', true);

    		filterComponent.setModuleCategoryVocabulary();
    		set_store_value(categoryCheckedTrack, $categoryCheckedTrack[$activeTab] = $moduleCategoryFilter, $categoryCheckedTrack);
    		set_store_value(moduleCategoryFilter, $moduleCategoryFilter = [], $moduleCategoryFilter);
    		set_store_value(activeTab, $activeTab = event.detail.pluginId, $activeTab);

    		set_store_value(
    			moduleCategoryFilter,
    			$moduleCategoryFilter = typeof $categoryCheckedTrack[$activeTab] !== 'undefined'
    			? $categoryCheckedTrack[$activeTab]
    			: [],
    			$moduleCategoryFilter
    		);

    		set_store_value(sortCriteria, $sortCriteria = SORT_OPTIONS[$activeTab], $sortCriteria);
    		const sortMatch = $sortCriteria.find(option => option.id === $sort);

    		if (typeof sortMatch === 'undefined') {
    			set_store_value(sort, $sort = $sortCriteria[0].id, $sort);
    		}

    		// Move to page 0 when switching sources as there's no guarantee the new
    		// source has enough results to reach whatever the current page is.
    		page.set(0);

    		await load(0);
    	}

    	/**
     * Refreshes the live region after a filter or search completes.
     */
    	const refreshLiveRegion = () => {
    		if ($rowsCount) {
    			// Set announce() to an empty string. This ensures the result count will
    			// be announced after filtering even if the count is the same.
    			announce('');

    			// The announcement is delayed by 210 milliseconds, a wait that is
    			// slightly longer than the 200 millisecond debounce() built into
    			// announce(). This ensures that the above call to reset the aria live
    			// region to an empty string actually takes place instead of being
    			// debounced.
    			setTimeout(
    				() => {
    					announce(window.Drupal.t('@count Results for @active_tab, Sorted by @sortText', {
    						'@count': numberFormatter.format($rowsCount),
    						'@sortText': sortText,
    						'@active_tab': ACTIVE_PLUGINS[$activeTab]
    					}));
    				},
    				210
    			);
    		}
    	};

    	document.onmouseover = function setInnerDocClickTrue() {
    		window.innerDocClick = true;
    	};

    	document.onmouseleave = function setInnerDocClickFalse() {
    		window.innerDocClick = false;
    	};

    	// Handles back button functionality to go back to the previous page the user was on before.
    	window.addEventListener('popstate', () => {
    		// Confirm the popstate event was a back button action by checking that
    		// the user clicked out of the document.
    		if (!window.innerDocClick) {
    			page.set($previousPage);
    			load($page);
    		}
    	});

    	window.onload = { onSearch };

    	// Removes initial loader if it exists.
    	const initialLoader = document.getElementById('initial-loader');

    	if (initialLoader) {
    		initialLoader.remove();
    	}

    	function filter_binding($$value) {
    		binding_callbacks[$$value ? 'unshift' : 'push'](() => {
    			filterComponent = $$value;
    			$$invalidate(5, filterComponent);
    		});
    	}

    	function search_binding($$value) {
    		binding_callbacks[$$value ? 'unshift' : 'push'](() => {
    			searchComponent = $$value;
    			$$invalidate(6, searchComponent);
    		});
    	}

    	const click_handler = e => {
    		$$invalidate(4, toggleView = 'List');
    		onToggle(e.target.value);
    	};

    	const click_handler_1 = e => {
    		$$invalidate(4, toggleView = 'Grid');
    		onToggle(e.target.value);
    	};

    	$$self.$$set = $$props => {
    		if ('searchText' in $$props) $$invalidate(0, searchText = $$props.searchText);
    	};

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty[0] & /*$page*/ 2) {
    			set_store_value(currentPage, $currentPage = $page, $currentPage);
    		}
    	};

    	return [
    		searchText,
    		$page,
    		dataArray,
    		loading,
    		toggleView,
    		filterComponent,
    		searchComponent,
    		$activeTab,
    		$rowsCount,
    		$pageSize,
    		rows,
    		currentPage,
    		previousPage,
    		onPageChange,
    		onPageSizeChange,
    		onSearch,
    		onSelectCategory,
    		onSort,
    		onAdvancedFilter,
    		onToggle,
    		toggleRows,
    		refreshLiveRegion,
    		filter_binding,
    		search_binding,
    		click_handler,
    		click_handler_1
    	];
    }

    class ProjectBrowser extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$3, create_fragment$3, safe_not_equal, { searchText: 0 }, null, [-1, -1]);
    	}
    }

    /* src/ImageCarousel.svelte generated by Svelte v3.48.0 */

    function create_if_block_1$2(ctx) {
    	let button;
    	let img;
    	let mounted;
    	let dispose;
    	let img_levels = [/*imgProps*/ ctx[4]('left')];
    	let img_data = {};

    	for (let i = 0; i < img_levels.length; i += 1) {
    		img_data = assign(img_data, img_levels[i]);
    	}

    	let button_levels = [/*buttonProps*/ ctx[3]('left')];
    	let button_data = {};

    	for (let i = 0; i < button_levels.length; i += 1) {
    		button_data = assign(button_data, button_levels[i]);
    	}

    	return {
    		c() {
    			button = element("button");
    			img = element("img");
    			set_attributes(img, img_data);
    			set_attributes(button, button_data);
    		},
    		m(target, anchor) {
    			insert(target, button, anchor);
    			append(button, img);
    			if (button.autofocus) button.focus();

    			if (!mounted) {
    				dispose = listen(button, "click", /*click_handler*/ ctx[5]);
    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			set_attributes(img, img_data = get_spread_update(img_levels, [/*imgProps*/ ctx[4]('left')]));
    			set_attributes(button, button_data = get_spread_update(button_levels, [/*buttonProps*/ ctx[3]('left')]));
    		},
    		d(detaching) {
    			if (detaching) detach(button);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (64:2) {#if sources.length}
    function create_if_block$2(ctx) {
    	let button;
    	let img;
    	let mounted;
    	let dispose;
    	let img_levels = [/*imgProps*/ ctx[4]('right')];
    	let img_data = {};

    	for (let i = 0; i < img_levels.length; i += 1) {
    		img_data = assign(img_data, img_levels[i]);
    	}

    	let button_levels = [/*buttonProps*/ ctx[3]('right')];
    	let button_data = {};

    	for (let i = 0; i < button_levels.length; i += 1) {
    		button_data = assign(button_data, button_levels[i]);
    	}

    	return {
    		c() {
    			button = element("button");
    			img = element("img");
    			set_attributes(img, img_data);
    			set_attributes(button, button_data);
    		},
    		m(target, anchor) {
    			insert(target, button, anchor);
    			append(button, img);
    			if (button.autofocus) button.focus();

    			if (!mounted) {
    				dispose = listen(button, "click", /*click_handler_1*/ ctx[6]);
    				mounted = true;
    			}
    		},
    		p(ctx, dirty) {
    			set_attributes(img, img_data = get_spread_update(img_levels, [/*imgProps*/ ctx[4]('right')]));
    			set_attributes(button, button_data = get_spread_update(button_levels, [/*buttonProps*/ ctx[3]('right')]));
    		},
    		d(detaching) {
    			if (detaching) detach(button);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    function create_fragment$2(ctx) {
    	let div;
    	let t0;
    	let image;
    	let t1;
    	let current;
    	let if_block0 = /*sources*/ ctx[0].length && create_if_block_1$2(ctx);

    	image = new Image({
    			props: {
    				sources: /*sources*/ ctx[0],
    				index: /*index*/ ctx[1],
    				class: "pb-image-carousel__slide"
    			}
    		});

    	let if_block1 = /*sources*/ ctx[0].length && create_if_block$2(ctx);

    	return {
    		c() {
    			div = element("div");
    			if (if_block0) if_block0.c();
    			t0 = space();
    			create_component(image.$$.fragment);
    			t1 = space();
    			if (if_block1) if_block1.c();
    			attr(div, "class", "pb-image-carousel");
    			attr(div, "aria-hidden", /*missingAltText*/ ctx[2]());
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			if (if_block0) if_block0.m(div, null);
    			append(div, t0);
    			mount_component(image, div, null);
    			append(div, t1);
    			if (if_block1) if_block1.m(div, null);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if (/*sources*/ ctx[0].length) {
    				if (if_block0) {
    					if_block0.p(ctx, dirty);
    				} else {
    					if_block0 = create_if_block_1$2(ctx);
    					if_block0.c();
    					if_block0.m(div, t0);
    				}
    			} else if (if_block0) {
    				if_block0.d(1);
    				if_block0 = null;
    			}

    			const image_changes = {};
    			if (dirty & /*sources*/ 1) image_changes.sources = /*sources*/ ctx[0];
    			if (dirty & /*index*/ 2) image_changes.index = /*index*/ ctx[1];
    			image.$set(image_changes);

    			if (/*sources*/ ctx[0].length) {
    				if (if_block1) {
    					if_block1.p(ctx, dirty);
    				} else {
    					if_block1 = create_if_block$2(ctx);
    					if_block1.c();
    					if_block1.m(div, null);
    				}
    			} else if (if_block1) {
    				if_block1.d(1);
    				if_block1 = null;
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(image.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(image.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			if (if_block0) if_block0.d();
    			destroy_component(image);
    			if (if_block1) if_block1.d();
    		}
    	};
    }

    function instance$2($$self, $$props, $$invalidate) {
    	let { sources } = $$props;
    	let index = 0;
    	const missingAltText = () => !!sources.filter(src => !src.alt).length;

    	/**
     * Props for a slide next/previous button.
     *
     * @param {string} dir
     *   The direction of the button.
     * @return {{disabled: boolean, class: string}}
     *   The slide props.
     */
    	const buttonProps = dir => {
    		const isDisabled = dir === 'right'
    		? index === sources.length - 1
    		: index === 0;

    		const classes = [
    			'pb-image-carousel__btn',
    			`pb-image-carousel__btn--${dir}`,
    			isDisabled ? 'pb-image-carousel__btn--disabled' : ''
    		];

    		return {
    			class: classes.filter(className => !!className).join(' '),
    			disabled: isDisabled
    		};
    	};

    	/**
     * Props for a slide next/previous button image.
     *
     * @param {string} dir
     *   The direction of the button
     * @return {{src: string, alt: *}}
     *   The slide button Props
     */
    	const imgProps = dir => ({
    		class: 'pb-image-carousel__btn-icon',
    		src: `${FULL_MODULE_PATH}/images/slide-icon.svg`,
    		alt: dir === 'right'
    		? window.Drupal.t('Slide right')
    		: window.Drupal.t('Slide left')
    	});

    	const click_handler = () => {
    		$$invalidate(1, index = (index + sources.length - 1) % sources.length);
    	};

    	const click_handler_1 = () => {
    		$$invalidate(1, index = (index + 1) % sources.length);
    	};

    	$$self.$$set = $$props => {
    		if ('sources' in $$props) $$invalidate(0, sources = $$props.sources);
    	};

    	return [
    		sources,
    		index,
    		missingAltText,
    		buttonProps,
    		imgProps,
    		click_handler,
    		click_handler_1
    	];
    }

    class ImageCarousel extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$2, create_fragment$2, safe_not_equal, { sources: 0 });
    	}
    }

    /* src/ModulePage.svelte generated by Svelte v3.48.0 */

    function get_each_context(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[6] = list[i];
    	return child_ctx;
    }

    // (45:6) {#if project.module_categories.length}
    function create_if_block_4(ctx) {
    	let p;
    	let t1;
    	let ul;
    	let each_value = /*project*/ ctx[0].module_categories || [];
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block(get_each_context(ctx, each_value, i));
    	}

    	return {
    		c() {
    			p = element("p");
    			p.textContent = `${window.Drupal.t('Categories:')}`;
    			t1 = space();
    			ul = element("ul");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			attr(p, "class", "pb-module-page__categories-label");
    			attr(p, "id", "categories");
    			attr(ul, "class", "pb-module-page__categories-list");
    			attr(ul, "aria-labelledby", "categories");
    		},
    		m(target, anchor) {
    			insert(target, p, anchor);
    			insert(target, t1, anchor);
    			insert(target, ul, anchor);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(ul, null);
    			}
    		},
    		p(ctx, dirty) {
    			if (dirty & /*filterByCategory, project*/ 3) {
    				each_value = /*project*/ ctx[0].module_categories || [];
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(ul, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}
    		},
    		d(detaching) {
    			if (detaching) detach(p);
    			if (detaching) detach(t1);
    			if (detaching) detach(ul);
    			destroy_each(each_blocks, detaching);
    		}
    	};
    }

    // (53:10) {#each project.module_categories || [] as category}
    function create_each_block(ctx) {
    	let li;
    	let t0_value = /*category*/ ctx[6].name + "";
    	let t0;
    	let t1;
    	let mounted;
    	let dispose;

    	function click_handler() {
    		return /*click_handler*/ ctx[2](/*category*/ ctx[6]);
    	}

    	return {
    		c() {
    			li = element("li");
    			t0 = text(t0_value);
    			t1 = space();
    			attr(li, "class", "pb-module-page__categories-list-item");
    		},
    		m(target, anchor) {
    			insert(target, li, anchor);
    			append(li, t0);
    			append(li, t1);

    			if (!mounted) {
    				dispose = listen(li, "click", click_handler);
    				mounted = true;
    			}
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			if (dirty & /*project*/ 1 && t0_value !== (t0_value = /*category*/ ctx[6].name + "")) set_data(t0, t0_value);
    		},
    		d(detaching) {
    			if (detaching) detach(li);
    			mounted = false;
    			dispose();
    		}
    	};
    }

    // (64:8) {#if project.is_compatible}
    function create_if_block_3(ctx) {
    	let projecticon;
    	let t0;
    	let p;
    	let current;

    	projecticon = new ProjectIcon({
    			props: {
    				type: "compatible",
    				variant: "module-details",
    				classes: "pb-module-page__module-details-icon"
    			}
    		});

    	return {
    		c() {
    			create_component(projecticon.$$.fragment);
    			t0 = space();
    			p = element("p");
    			p.textContent = `${window.Drupal.t('Compatible with your Drupal installation')}`;
    			attr(p, "class", "pb-module-page__module-details-info");
    		},
    		m(target, anchor) {
    			mount_component(projecticon, target, anchor);
    			insert(target, t0, anchor);
    			insert(target, p, anchor);
    			current = true;
    		},
    		p: noop,
    		i(local) {
    			if (current) return;
    			transition_in(projecticon.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projecticon.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projecticon, detaching);
    			if (detaching) detach(t0);
    			if (detaching) detach(p);
    		}
    	};
    }

    // (74:8) {#if project.project_usage_total !== -1}
    function create_if_block_2$1(ctx) {
    	let projecticon;
    	let t0;
    	let p;
    	let t1_value = numberFormatter.format(/*project*/ ctx[0].project_usage_total) + "";
    	let t1;
    	let t2_value = window.Drupal.t(' sites report using this module') + "";
    	let t2;
    	let current;

    	projecticon = new ProjectIcon({
    			props: {
    				type: "usage",
    				variant: "module-details",
    				classes: "pb-module-page__module-details-icon"
    			}
    		});

    	return {
    		c() {
    			create_component(projecticon.$$.fragment);
    			t0 = space();
    			p = element("p");
    			t1 = text(t1_value);
    			t2 = text(t2_value);
    			attr(p, "class", "pb-module-page__module-details-info");
    		},
    		m(target, anchor) {
    			mount_component(projecticon, target, anchor);
    			insert(target, t0, anchor);
    			insert(target, p, anchor);
    			append(p, t1);
    			append(p, t2);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if ((!current || dirty & /*project*/ 1) && t1_value !== (t1_value = numberFormatter.format(/*project*/ ctx[0].project_usage_total) + "")) set_data(t1, t1_value);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(projecticon.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projecticon.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projecticon, detaching);
    			if (detaching) detach(t0);
    			if (detaching) detach(p);
    		}
    	};
    }

    // (86:8) {#if project.is_covered}
    function create_if_block_1$1(ctx) {
    	let projecticon;
    	let t0;
    	let p;
    	let current;

    	projecticon = new ProjectIcon({
    			props: {
    				type: "status",
    				variant: "module-details",
    				classes: "pb-module-page__module-details-icon"
    			}
    		});

    	return {
    		c() {
    			create_component(projecticon.$$.fragment);
    			t0 = space();
    			p = element("p");
    			p.textContent = `${window.Drupal.t('Stable releases for this project are covered by the security advisory policy')}`;
    			attr(p, "class", "pb-module-page__module-details-info");
    		},
    		m(target, anchor) {
    			mount_component(projecticon, target, anchor);
    			insert(target, t0, anchor);
    			insert(target, p, anchor);
    			current = true;
    		},
    		p: noop,
    		i(local) {
    			if (current) return;
    			transition_in(projecticon.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projecticon.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projecticon, detaching);
    			if (detaching) detach(t0);
    			if (detaching) detach(p);
    		}
    	};
    }

    // (106:4) {#if project.project_images.length}
    function create_if_block$1(ctx) {
    	let div;
    	let imagecarousel;
    	let current;

    	imagecarousel = new ImageCarousel({
    			props: {
    				sources: /*project*/ ctx[0].project_images
    			}
    		});

    	return {
    		c() {
    			div = element("div");
    			create_component(imagecarousel.$$.fragment);
    			attr(div, "class", "pb-module-page__carousel-wrapper");
    		},
    		m(target, anchor) {
    			insert(target, div, anchor);
    			mount_component(imagecarousel, div, null);
    			current = true;
    		},
    		p(ctx, dirty) {
    			const imagecarousel_changes = {};
    			if (dirty & /*project*/ 1) imagecarousel_changes.sources = /*project*/ ctx[0].project_images;
    			imagecarousel.$set(imagecarousel_changes);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(imagecarousel.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(imagecarousel.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(div);
    			destroy_component(imagecarousel);
    		}
    	};
    }

    function create_fragment$1(ctx) {
    	let a;
    	let span;
    	let t1;
    	let t2_value = window.Drupal.t('Back to Browsing') + "";
    	let t2;
    	let t3;
    	let div6;
    	let div3;
    	let image;
    	let t4;
    	let div0;
    	let actionbutton;
    	let t5;
    	let hr;
    	let t6;
    	let div2;
    	let h4;
    	let t8;
    	let t9;
    	let div1;
    	let t10;
    	let t11;
    	let t12;
    	let div5;
    	let h2;
    	let t13_value = /*project*/ ctx[0].title + "";
    	let t13;
    	let t14;
    	let p;
    	let t15_value = window.Drupal.t('By ') + "";
    	let t15;
    	let t16_value = /*project*/ ctx[0].author.name + "";
    	let t16;
    	let t17;
    	let t18;
    	let div4;
    	let raw_value = /*project*/ ctx[0].body.value + "";
    	let current;

    	image = new Image({
    			props: {
    				sources: /*project*/ ctx[0].logo,
    				class: "pb-module-page__project-logo"
    			}
    		});

    	actionbutton = new ActionButton({ props: { project: /*project*/ ctx[0] } });
    	let if_block0 = /*project*/ ctx[0].module_categories.length && create_if_block_4(ctx);
    	let if_block1 = /*project*/ ctx[0].is_compatible && create_if_block_3();
    	let if_block2 = /*project*/ ctx[0].project_usage_total !== -1 && create_if_block_2$1(ctx);
    	let if_block3 = /*project*/ ctx[0].is_covered && create_if_block_1$1();
    	let if_block4 = /*project*/ ctx[0].project_images.length && create_if_block$1(ctx);

    	return {
    		c() {
    			a = element("a");
    			span = element("span");
    			span.textContent = "〈 ";
    			t1 = space();
    			t2 = text(t2_value);
    			t3 = space();
    			div6 = element("div");
    			div3 = element("div");
    			create_component(image.$$.fragment);
    			t4 = space();
    			div0 = element("div");
    			create_component(actionbutton.$$.fragment);
    			t5 = space();
    			hr = element("hr");
    			t6 = space();
    			div2 = element("div");
    			h4 = element("h4");
    			h4.textContent = `${window.Drupal.t('Details')}`;
    			t8 = space();
    			if (if_block0) if_block0.c();
    			t9 = space();
    			div1 = element("div");
    			if (if_block1) if_block1.c();
    			t10 = space();
    			if (if_block2) if_block2.c();
    			t11 = space();
    			if (if_block3) if_block3.c();
    			t12 = space();
    			div5 = element("div");
    			h2 = element("h2");
    			t13 = text(t13_value);
    			t14 = space();
    			p = element("p");
    			t15 = text(t15_value);
    			t16 = text(t16_value);
    			t17 = space();
    			if (if_block4) if_block4.c();
    			t18 = space();
    			div4 = element("div");
    			attr(span, "aria-hidden", "true");
    			attr(a, "class", "action-link");
    			attr(a, "href", "" + (ORIGIN_URL + "/admin/modules/browse"));
    			attr(div0, "class", "pb-module-page__actions");
    			attr(h4, "class", "pb-module-page__details-title");
    			attr(div1, "class", "pb-module-page__module-details");
    			attr(div2, "class", "pb-module-page__details");
    			attr(div3, "class", "pb-module-page__sidebar");
    			attr(h2, "class", "pb-module-page__title");
    			attr(p, "class", "pb-module-page__author");
    			attr(div4, "class", "pb-module-page__description");
    			attr(div4, "id", "description-wrapper");
    			attr(div5, "class", "pb-module-page__main");
    			attr(div6, "class", "pb-module-page");
    		},
    		m(target, anchor) {
    			insert(target, a, anchor);
    			append(a, span);
    			append(a, t1);
    			append(a, t2);
    			insert(target, t3, anchor);
    			insert(target, div6, anchor);
    			append(div6, div3);
    			mount_component(image, div3, null);
    			append(div3, t4);
    			append(div3, div0);
    			mount_component(actionbutton, div0, null);
    			append(div3, t5);
    			append(div3, hr);
    			append(div3, t6);
    			append(div3, div2);
    			append(div2, h4);
    			append(div2, t8);
    			if (if_block0) if_block0.m(div2, null);
    			append(div2, t9);
    			append(div2, div1);
    			if (if_block1) if_block1.m(div1, null);
    			append(div1, t10);
    			if (if_block2) if_block2.m(div1, null);
    			append(div1, t11);
    			if (if_block3) if_block3.m(div1, null);
    			append(div6, t12);
    			append(div6, div5);
    			append(div5, h2);
    			append(h2, t13);
    			append(div5, t14);
    			append(div5, p);
    			append(p, t15);
    			append(p, t16);
    			append(div5, t17);
    			if (if_block4) if_block4.m(div5, null);
    			append(div5, t18);
    			append(div5, div4);
    			div4.innerHTML = raw_value;
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			const image_changes = {};
    			if (dirty & /*project*/ 1) image_changes.sources = /*project*/ ctx[0].logo;
    			image.$set(image_changes);
    			const actionbutton_changes = {};
    			if (dirty & /*project*/ 1) actionbutton_changes.project = /*project*/ ctx[0];
    			actionbutton.$set(actionbutton_changes);

    			if (/*project*/ ctx[0].module_categories.length) {
    				if (if_block0) {
    					if_block0.p(ctx, dirty);
    				} else {
    					if_block0 = create_if_block_4(ctx);
    					if_block0.c();
    					if_block0.m(div2, t9);
    				}
    			} else if (if_block0) {
    				if_block0.d(1);
    				if_block0 = null;
    			}

    			if (/*project*/ ctx[0].is_compatible) {
    				if (if_block1) {
    					if_block1.p(ctx, dirty);

    					if (dirty & /*project*/ 1) {
    						transition_in(if_block1, 1);
    					}
    				} else {
    					if_block1 = create_if_block_3();
    					if_block1.c();
    					transition_in(if_block1, 1);
    					if_block1.m(div1, t10);
    				}
    			} else if (if_block1) {
    				group_outros();

    				transition_out(if_block1, 1, 1, () => {
    					if_block1 = null;
    				});

    				check_outros();
    			}

    			if (/*project*/ ctx[0].project_usage_total !== -1) {
    				if (if_block2) {
    					if_block2.p(ctx, dirty);

    					if (dirty & /*project*/ 1) {
    						transition_in(if_block2, 1);
    					}
    				} else {
    					if_block2 = create_if_block_2$1(ctx);
    					if_block2.c();
    					transition_in(if_block2, 1);
    					if_block2.m(div1, t11);
    				}
    			} else if (if_block2) {
    				group_outros();

    				transition_out(if_block2, 1, 1, () => {
    					if_block2 = null;
    				});

    				check_outros();
    			}

    			if (/*project*/ ctx[0].is_covered) {
    				if (if_block3) {
    					if_block3.p(ctx, dirty);

    					if (dirty & /*project*/ 1) {
    						transition_in(if_block3, 1);
    					}
    				} else {
    					if_block3 = create_if_block_1$1();
    					if_block3.c();
    					transition_in(if_block3, 1);
    					if_block3.m(div1, null);
    				}
    			} else if (if_block3) {
    				group_outros();

    				transition_out(if_block3, 1, 1, () => {
    					if_block3 = null;
    				});

    				check_outros();
    			}

    			if ((!current || dirty & /*project*/ 1) && t13_value !== (t13_value = /*project*/ ctx[0].title + "")) set_data(t13, t13_value);
    			if ((!current || dirty & /*project*/ 1) && t16_value !== (t16_value = /*project*/ ctx[0].author.name + "")) set_data(t16, t16_value);

    			if (/*project*/ ctx[0].project_images.length) {
    				if (if_block4) {
    					if_block4.p(ctx, dirty);

    					if (dirty & /*project*/ 1) {
    						transition_in(if_block4, 1);
    					}
    				} else {
    					if_block4 = create_if_block$1(ctx);
    					if_block4.c();
    					transition_in(if_block4, 1);
    					if_block4.m(div5, t18);
    				}
    			} else if (if_block4) {
    				group_outros();

    				transition_out(if_block4, 1, 1, () => {
    					if_block4 = null;
    				});

    				check_outros();
    			}

    			if ((!current || dirty & /*project*/ 1) && raw_value !== (raw_value = /*project*/ ctx[0].body.value + "")) div4.innerHTML = raw_value;		},
    		i(local) {
    			if (current) return;
    			transition_in(image.$$.fragment, local);
    			transition_in(actionbutton.$$.fragment, local);
    			transition_in(if_block1);
    			transition_in(if_block2);
    			transition_in(if_block3);
    			transition_in(if_block4);
    			current = true;
    		},
    		o(local) {
    			transition_out(image.$$.fragment, local);
    			transition_out(actionbutton.$$.fragment, local);
    			transition_out(if_block1);
    			transition_out(if_block2);
    			transition_out(if_block3);
    			transition_out(if_block4);
    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(a);
    			if (detaching) detach(t3);
    			if (detaching) detach(div6);
    			destroy_component(image);
    			destroy_component(actionbutton);
    			if (if_block0) if_block0.d();
    			if (if_block1) if_block1.d();
    			if (if_block2) if_block2.d();
    			if (if_block3) if_block3.d();
    			if (if_block4) if_block4.d();
    		}
    	};
    }

    function instance$1($$self, $$props, $$invalidate) {
    	let $page;
    	let $moduleCategoryFilter;
    	component_subscribe($$self, page, $$value => $$invalidate(3, $page = $$value));
    	component_subscribe($$self, moduleCategoryFilter, $$value => $$invalidate(4, $moduleCategoryFilter = $$value));
    	let { project } = $$props;

    	function filterByCategory(id) {
    		set_store_value(moduleCategoryFilter, $moduleCategoryFilter = [id], $moduleCategoryFilter);
    		set_store_value(page, $page = 0, $page);
    		window.location.href = `${ORIGIN_URL}/admin/modules/browse`;
    	}

    	onMount(() => {
    		const anchors = document.getElementById('description-wrapper').getElementsByTagName('a');

    		for (let i = 0; i < anchors.length; i++) {
    			anchors[i].setAttribute('target', '_blank');
    		}
    	});

    	const click_handler = category => filterByCategory(category.id);

    	$$self.$$set = $$props => {
    		if ('project' in $$props) $$invalidate(0, project = $$props.project);
    	};

    	return [project, filterByCategory, click_handler];
    }

    class ModulePage extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance$1, create_fragment$1, safe_not_equal, { project: 0 });
    	}
    }

    /* src/App.svelte generated by Svelte v3.48.0 */

    function create_else_block(ctx) {
    	let await_block_anchor;
    	let current;

    	let info = {
    		ctx,
    		current: null,
    		token: null,
    		hasCatch: false,
    		pending: create_pending_block,
    		then: create_then_block,
    		catch: create_catch_block,
    		value: 2,
    		blocks: [,,,]
    	};

    	handle_promise(/*load*/ ctx[4](`${ORIGIN_URL}/drupal-org-proxy/project?machine_name=${/*moduleName*/ ctx[3]}`), info);

    	return {
    		c() {
    			await_block_anchor = empty();
    			info.block.c();
    		},
    		m(target, anchor) {
    			insert(target, await_block_anchor, anchor);
    			info.block.m(target, info.anchor = anchor);
    			info.mount = () => await_block_anchor.parentNode;
    			info.anchor = await_block_anchor;
    			current = true;
    		},
    		p(new_ctx, dirty) {
    			ctx = new_ctx;
    			update_await_block_branch(info, ctx, dirty);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(info.block);
    			current = true;
    		},
    		o(local) {
    			for (let i = 0; i < 3; i += 1) {
    				const block = info.blocks[i];
    				transition_out(block);
    			}

    			current = false;
    		},
    		d(detaching) {
    			if (detaching) detach(await_block_anchor);
    			info.block.d(detaching);
    			info.token = null;
    			info = null;
    		}
    	};
    }

    // (45:0) {#if !moduleName}
    function create_if_block(ctx) {
    	let projectbrowser;
    	let current;
    	projectbrowser = new ProjectBrowser({});

    	return {
    		c() {
    			create_component(projectbrowser.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(projectbrowser, target, anchor);
    			current = true;
    		},
    		p: noop,
    		i(local) {
    			if (current) return;
    			transition_in(projectbrowser.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectbrowser.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projectbrowser, detaching);
    		}
    	};
    }

    // (1:0) <script>   import ProjectBrowser from './ProjectBrowser.svelte';   import ModulePage from './ModulePage.svelte';   import Loading from './Loading.svelte';   import { searchString, activeTab }
    function create_catch_block(ctx) {
    	return {
    		c: noop,
    		m: noop,
    		p: noop,
    		i: noop,
    		o: noop,
    		d: noop
    	};
    }

    // (52:2) {:then project}
    function create_then_block(ctx) {
    	let current_block_type_index;
    	let if_block;
    	let if_block_anchor;
    	let current;
    	const if_block_creators = [create_if_block_2, create_else_block_1];
    	const if_blocks = [];

    	function select_block_type_1(ctx, dirty) {
    		if (/*projectExists*/ ctx[1]) return 0;
    		return 1;
    	}

    	current_block_type_index = select_block_type_1(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	return {
    		c() {
    			if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if_blocks[current_block_type_index].m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type_1(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if_blocks[current_block_type_index].p(ctx, dirty);
    			} else {
    				group_outros();

    				transition_out(if_blocks[previous_block_index], 1, 1, () => {
    					if_blocks[previous_block_index] = null;
    				});

    				check_outros();
    				if_block = if_blocks[current_block_type_index];

    				if (!if_block) {
    					if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    					if_block.c();
    				} else {
    					if_block.p(ctx, dirty);
    				}

    				transition_in(if_block, 1);
    				if_block.m(if_block_anchor.parentNode, if_block_anchor);
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if_blocks[current_block_type_index].d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (55:4) {:else}
    function create_else_block_1(ctx) {
    	let projectbrowser;
    	let current;
    	projectbrowser = new ProjectBrowser({});

    	return {
    		c() {
    			create_component(projectbrowser.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(projectbrowser, target, anchor);
    			current = true;
    		},
    		p: noop,
    		i(local) {
    			if (current) return;
    			transition_in(projectbrowser.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(projectbrowser.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(projectbrowser, detaching);
    		}
    	};
    }

    // (53:4) {#if projectExists}
    function create_if_block_2(ctx) {
    	let modulepage;
    	let current;
    	modulepage = new ModulePage({ props: { project: /*project*/ ctx[2] } });

    	return {
    		c() {
    			create_component(modulepage.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(modulepage, target, anchor);
    			current = true;
    		},
    		p: noop,
    		i(local) {
    			if (current) return;
    			transition_in(modulepage.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(modulepage.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(modulepage, detaching);
    		}
    	};
    }

    // (48:84)      {#if loading}
    function create_pending_block(ctx) {
    	let if_block_anchor;
    	let current;
    	let if_block = /*loading*/ ctx[0] && create_if_block_1();

    	return {
    		c() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, dirty) {
    			if (/*loading*/ ctx[0]) {
    				if (if_block) {
    					if (dirty & /*loading*/ 1) {
    						transition_in(if_block, 1);
    					}
    				} else {
    					if_block = create_if_block_1();
    					if_block.c();
    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				group_outros();

    				transition_out(if_block, 1, 1, () => {
    					if_block = null;
    				});

    				check_outros();
    			}
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    // (49:4) {#if loading}
    function create_if_block_1(ctx) {
    	let loading_1;
    	let current;
    	loading_1 = new Loading({});

    	return {
    		c() {
    			create_component(loading_1.$$.fragment);
    		},
    		m(target, anchor) {
    			mount_component(loading_1, target, anchor);
    			current = true;
    		},
    		i(local) {
    			if (current) return;
    			transition_in(loading_1.$$.fragment, local);
    			current = true;
    		},
    		o(local) {
    			transition_out(loading_1.$$.fragment, local);
    			current = false;
    		},
    		d(detaching) {
    			destroy_component(loading_1, detaching);
    		}
    	};
    }

    function create_fragment(ctx) {
    	let current_block_type_index;
    	let if_block;
    	let if_block_anchor;
    	let current;
    	const if_block_creators = [create_if_block, create_else_block];
    	const if_blocks = [];

    	function select_block_type(ctx, dirty) {
    		if (!/*moduleName*/ ctx[3]) return 0;
    		return 1;
    	}

    	current_block_type_index = select_block_type(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	return {
    		c() {
    			if_block.c();
    			if_block_anchor = empty();
    		},
    		m(target, anchor) {
    			if_blocks[current_block_type_index].m(target, anchor);
    			insert(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p(ctx, [dirty]) {
    			if_block.p(ctx, dirty);
    		},
    		i(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d(detaching) {
    			if_blocks[current_block_type_index].d(detaching);
    			if (detaching) detach(if_block_anchor);
    		}
    	};
    }

    function instance($$self, $$props, $$invalidate) {
    	let $searchString;
    	let $activeTab;
    	component_subscribe($$self, searchString, $$value => $$invalidate(6, $searchString = $$value));
    	component_subscribe($$self, activeTab, $$value => $$invalidate(7, $activeTab = $$value));
    	const matches = window.location.pathname.match(/\/admin\/modules\/browse\/([^/]+)/);
    	const moduleName = matches ? matches[1] : null;
    	let loading = true;
    	let data;
    	let project = [];
    	let projectExists = false;

    	async function load(url) {
    		$$invalidate(0, loading = true);
    		const res = await fetch(url);

    		if (res.ok) {
    			data = await res.json();

    			Object.entries(data).forEach(item => {
    				const [source, result] = item;

    				if (result.totalResults !== 0) {
    					set_store_value(activeTab, $activeTab = source, $activeTab);
    					$$invalidate(2, [project] = result.list, project);
    					$$invalidate(1, projectExists = true);
    				}
    			});
    		}

    		$$invalidate(0, loading = false);

    		if (!projectExists) {
    			set_store_value(searchString, $searchString = moduleName, $searchString);
    		}

    		return project;
    	}

    	// Removes initial loader if it exists.
    	const initialLoader = document.getElementById('initial-loader');

    	if (initialLoader) {
    		initialLoader.remove();
    	}

    	return [loading, projectExists, project, moduleName, load];
    }

    class App extends SvelteComponent {
    	constructor(options) {
    		super();
    		init(this, options, instance, create_fragment, safe_not_equal, {});
    	}
    }

    const app = new App({
      // The #project-browser markup is returned by the project_browser.browse Drupal route.
      target: document.querySelector('#project-browser'),
      props: {},
    });

    return app;

})();
//# sourceMappingURL=bundle.js.map
