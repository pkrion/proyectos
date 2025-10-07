(function () {
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    function initCredentialCopy() {
        document.querySelectorAll('[data-copy]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                var target = event.currentTarget;
                var text = target.dataset.copy || '';
                if (!text) {
                    return;
                }

                navigator.clipboard.writeText(text).then(function () {
                    target.classList.add('btn-success');
                    target.textContent = target.dataset.copiedLabel || 'Copiado';
                    setTimeout(function () {
                        target.classList.remove('btn-success');
                        target.textContent = target.dataset.label || 'Copiar';
                    }, 1800);
                });
            });
        });
    }

    function initKanban() {
        var board = document.querySelector('[data-mo-kanban]');
        if (!board) {
            return;
        }

        var apiEndpoint = board.dataset.api || '';

        board.querySelectorAll('[data-mo-column]').forEach(function (column) {
            column.addEventListener('dragover', function (event) {
                event.preventDefault();
                column.classList.add('mo-kanban-dropzone--active');
            });

            column.addEventListener('dragleave', function () {
                column.classList.remove('mo-kanban-dropzone--active');
            });

            column.addEventListener('drop', function (event) {
                event.preventDefault();
                column.classList.remove('mo-kanban-dropzone--active');

                var taskId = event.dataTransfer.getData('text/plain');
                var task = document.querySelector('[data-mo-task="' + taskId + '"]');
                if (!task) {
                    return;
                }

                var list = column.querySelector('[data-mo-task-list]');
                list.appendChild(task);

                reorderTasks(column, apiEndpoint);
            });
        });

        board.querySelectorAll('[data-mo-task]').forEach(function (task) {
            task.setAttribute('draggable', 'true');
            task.addEventListener('dragstart', function (event) {
                event.dataTransfer.setData('text/plain', task.dataset.moTask);
                event.dataTransfer.effectAllowed = 'move';
                task.classList.add('shadow-lg');
            });
            task.addEventListener('dragend', function () {
                task.classList.remove('shadow-lg');
            });
        });
    }

    function reorderTasks(column, apiEndpoint) {
        var list = column.querySelectorAll('[data-mo-task]');
        var payload = [];
        list.forEach(function (task, index) {
            payload.push({
                id: task.dataset.moTask,
                position: index * 10,
                status: column.dataset.moColumn,
            });
        });

        if (!apiEndpoint || payload.length === 0) {
            return;
        }

        fetch(apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ tasks: payload })
        }).catch(function (error) {
            console.error('No se pudo actualizar el tablero Kanban', error);
        });
    }

    ready(function () {
        initCredentialCopy();
        initKanban();
    });
})();
