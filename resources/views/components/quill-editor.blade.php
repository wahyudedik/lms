@props(['name' => 'content', 'value' => '', 'placeholder' => ''])

@once
    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <style>
            .ql-editor {
                min-height: 120px;
                font-size: 0.875rem;
            }

            .ql-toolbar.ql-snow {
                border-radius: 0.5rem 0.5rem 0 0;
                border-color: #d1d5db;
            }

            .ql-container.ql-snow {
                border-radius: 0 0 0.5rem 0.5rem;
                border-color: #d1d5db;
            }

            .ql-editor.ql-blank::before {
                font-style: normal;
                color: #9ca3af;
            }
        </style>
    @endpush
@endonce

<div x-data="{ content: '' }" x-init="content = $refs.hiddenInput.value">
    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" value="{{ old($name, $value) }}">
    <div x-ref="quillEditor" data-placeholder="{{ $placeholder }}" class="quill-editor-instance"></div>
</div>

@once
    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.quill-editor-instance').forEach(function(editorEl) {
                    const container = editorEl.closest('[x-data]');
                    const hiddenInput = container.querySelector('input[type="hidden"]');
                    const placeholder = editorEl.dataset.placeholder || '';

                    const quill = new Quill(editorEl, {
                        theme: 'snow',
                        placeholder: placeholder,
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                [{
                                    'list': 'ordered'
                                }, {
                                    'list': 'bullet'
                                }],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });

                    // Set initial content
                    if (hiddenInput.value) {
                        quill.root.innerHTML = hiddenInput.value;
                    }

                    // Sync content to hidden input
                    quill.on('text-change', function() {
                        const html = quill.root.innerHTML;
                        hiddenInput.value = html === '<p><br></p>' ? '' : html;
                    });
                });
            });
        </script>
    @endpush
@endonce
