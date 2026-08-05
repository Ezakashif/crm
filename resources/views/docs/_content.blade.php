<style>
    .crm-docs-content { line-height: 1.6; }
    .crm-docs-content h1, .crm-docs-content h2, .crm-docs-content h3 { margin-top: 1.25rem; }
    .crm-docs-content pre { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: .25rem; padding: .75rem; overflow-x: auto; }
    .crm-docs-content code { font-size: .9em; }
    .crm-docs-content table { width: 100%; margin-bottom: 1rem; }
    .crm-docs-content table th, .crm-docs-content table td { border: 1px solid #dee2e6; padding: .4rem .6rem; }
    .crm-docs-content blockquote { border-left: 4px solid #17a2b8; padding-left: .75rem; color: #555; }
</style>

<div class="row">
    <div class="col-lg-3 mb-3">
        <div class="card card-outline card-secondary">
            <div class="card-header"><strong>Contents</strong></div>
            <div class="list-group list-group-flush small" style="max-height: 70vh; overflow-y: auto;">
                @foreach ($nav as $group)
                    <div class="list-group-item bg-light text-uppercase text-muted font-weight-bold"
                         style="letter-spacing: .03em; font-size: .72rem;">
                        {{ $group['section'] }}
                    </div>
                    @foreach ($group['items'] as $item)
                        <a href="{{ $item['url'] }}"
                           class="list-group-item list-group-item-action {{ $path === $item['path'] ? 'active' : '' }}">
                            {{ $item['title'] }}
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-9 mb-3">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                <strong>{{ $title }}</strong>
                <code class="small text-muted">docs/{{ $path === 'README' ? 'README.md' : $path.'.md' }}</code>
            </div>
            <div class="card-body crm-docs-content">
                {!! $html !!}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.crm-docs-content pre code.language-mermaid').forEach(function (block) {
            var pre = block.parentElement;
            var div = document.createElement('div');
            div.className = 'mermaid';
            div.textContent = block.textContent;
            pre.replaceWith(div);
        });
        if (window.mermaid) {
            mermaid.initialize({ startOnLoad: true, theme: 'neutral' });
        }
    });
</script>
