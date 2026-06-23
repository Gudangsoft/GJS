<?php $__env->startPush('head'); ?>

<link rel="canonical" href="<?php echo e(route('journals.articles.show', [$journal->slug, $article->id])); ?>">


<meta property="og:type" content="article">
<meta property="og:title" content="<?php echo e($article->submission->title); ?>">
<meta property="og:description" content="<?php echo e(Str::limit($article->submission->abstract, 200)); ?>">
<meta property="og:url" content="<?php echo e(route('journals.articles.show', [$journal->slug, $article->id])); ?>">
<meta property="og:site_name" content="<?php echo e($journal->name); ?>">
<?php if($article->date_published): ?>
<meta property="article:published_time" content="<?php echo e($article->date_published->toIso8601String()); ?>">
<?php endif; ?>


<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ScholarlyArticle",
    "headline": <?php echo e(json_encode($article->submission->title)); ?>,
    <?php if($article->submission->abstract): ?>
    "description": <?php echo e(json_encode(Str::limit($article->submission->abstract, 500))); ?>,
    "abstract": <?php echo e(json_encode($article->submission->abstract)); ?>,
    <?php endif; ?>
    "url": <?php echo e(json_encode(route('journals.articles.show', [$journal->slug, $article->id]))); ?>,
    <?php if($article->doi): ?>
    "identifier": <?php echo e(json_encode('https://doi.org/' . $article->doi)); ?>,
    <?php endif; ?>
    <?php if($article->date_published): ?>
    "datePublished": <?php echo e(json_encode($article->date_published->toDateString())); ?>,
    <?php endif; ?>
    "inLanguage": <?php echo e(json_encode($article->submission->locale ?? 'id')); ?>,
    "author": [
        <?php $__currentLoopData = $article->submission->contributors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $contributor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            "@type": "Person",
            "name": <?php echo e(json_encode($contributor->full_name)); ?>

            <?php if($contributor->affiliation): ?>,"affiliation": {"@type": "Organization", "name": <?php echo e(json_encode($contributor->affiliation)); ?>}<?php endif; ?>
            <?php if($contributor->orcid): ?>,"identifier": <?php echo e(json_encode('https://orcid.org/' . $contributor->orcid)); ?><?php endif; ?>
        }<?php echo e(!$loop->last ? ',' : ''); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    ],
    "isPartOf": {
        "@type": "Periodical",
        "name": <?php echo e(json_encode($journal->name)); ?>

        <?php if($journal->issn_online): ?>,"issn": <?php echo e(json_encode($journal->issn_online)); ?><?php elseif($journal->issn_print): ?>,"issn": <?php echo e(json_encode($journal->issn_print)); ?><?php endif; ?>
    },
    "publisher": {
        "@type": "Organization",
        "name": <?php echo e(json_encode($journal->publisher ?? $journal->name)); ?>

    },
    "license": "https://creativecommons.org/licenses/by/4.0/"
}
</script>


<meta name="citation_title" content="<?php echo e($article->submission->title); ?>">
<?php $__currentLoopData = $article->submission->contributors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contributor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<meta name="citation_author" content="<?php echo e($contributor->last_name); ?>, <?php echo e($contributor->first_name); ?>">
<?php if($contributor->affiliation): ?>
<meta name="citation_author_institution" content="<?php echo e($contributor->affiliation); ?>">
<?php endif; ?>
<?php if($contributor->orcid): ?>
<meta name="citation_author_orcid" content="https://orcid.org/<?php echo e($contributor->orcid); ?>">
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<meta name="citation_journal_title" content="<?php echo e($journal->name); ?>">
<?php if($journal->issn_online): ?>
<meta name="citation_issn" content="<?php echo e($journal->issn_online); ?>">
<?php elseif($journal->issn_print): ?>
<meta name="citation_issn" content="<?php echo e($journal->issn_print); ?>">
<?php endif; ?>
<?php if($article->issue): ?>
<?php if($article->issue->volume): ?><meta name="citation_volume" content="<?php echo e($article->issue->volume); ?>"><?php endif; ?>
<?php if($article->issue->number): ?><meta name="citation_issue" content="<?php echo e($article->issue->number); ?>"><?php endif; ?>
<?php if($article->issue->year): ?><meta name="citation_year" content="<?php echo e($article->issue->year); ?>"><?php endif; ?>
<?php endif; ?>
<?php if($article->date_published): ?>
<meta name="citation_publication_date" content="<?php echo e($article->date_published->format('Y/m/d')); ?>">
<?php endif; ?>
<?php if($article->pages): ?>
<?php [$pageFirst, $pageLast] = array_pad(explode('-', $article->pages, 2), 2, null); ?>
<meta name="citation_firstpage" content="<?php echo e(trim($pageFirst)); ?>">
<?php if($pageLast): ?><meta name="citation_lastpage" content="<?php echo e(trim($pageLast)); ?>"><?php endif; ?>
<?php endif; ?>
<?php if($article->doi): ?>
<meta name="citation_doi" content="<?php echo e($article->doi); ?>">
<?php endif; ?>
<?php if($article->submission->abstract): ?>
<meta name="citation_abstract" content="<?php echo e($article->submission->abstract); ?>">
<?php endif; ?>
<?php if($article->submission->keywords): ?>
<?php $__currentLoopData = $article->submission->keywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<meta name="citation_keyword" content="<?php echo e($kw); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<meta name="citation_language" content="<?php echo e($article->submission->locale ?? 'id'); ?>">
<meta name="citation_abstract_html_url" content="<?php echo e(route('journals.articles.show', [$journal->slug, $article->id])); ?>">
<?php $__currentLoopData = $galleys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galley): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if(str_contains(strtolower($galley['label']), 'pdf')): ?>
<meta name="citation_pdf_url" content="<?php echo e(route('journals.articles.galley', [$journal->slug, $article->id, $galley['id']])); ?>">
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopPush(); ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6 flex-wrap">
        <a href="<?php echo e(route('journals.home', $journal->slug)); ?>" class="hover:text-blue-600"><?php echo e($journal->name); ?></a>
        <?php if($article->issue): ?>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="<?php echo e(route('journals.issues.show', [$journal->slug, $article->issue->id])); ?>" class="hover:text-blue-600">
            <?php echo e($article->issue->getLabel()); ?>

        </a>
        <?php endif; ?>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 truncate max-w-xs"><?php echo e(Str::limit($article->submission->title, 50)); ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 p-7">

                
                <?php if($article->section): ?>
                <span class="inline-block text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full mb-3">
                    <?php echo e($article->section->title); ?>

                </span>
                <?php endif; ?>

                
                <h1 class="text-2xl font-bold text-slate-900 leading-tight mb-2">
                    <?php echo e($article->submission->title); ?>

                </h1>
                <?php if($article->submission->subtitle): ?>
                <p class="text-lg text-slate-600 mb-4"><?php echo e($article->submission->subtitle); ?></p>
                <?php endif; ?>

                
                <div class="mb-5 pb-5 border-b border-slate-100">
                    <p class="text-sm font-medium text-slate-700 mb-1">Penulis</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1">
                        <?php $__currentLoopData = $article->submission->contributors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contributor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="text-sm">
                            <span class="font-medium text-slate-800"><?php echo e($contributor->full_name); ?></span>
                            <?php if($contributor->affiliation): ?>
                            <span class="text-slate-500">, <?php echo e($contributor->affiliation); ?></span>
                            <?php endif; ?>
                            <?php if($contributor->orcid): ?>
                            <a href="https://orcid.org/<?php echo e($contributor->orcid); ?>" target="_blank" rel="noopener"
                               class="ml-1 text-xs text-green-600 hover:underline">ORCID</a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <?php if($article->submission->abstract): ?>
                <div class="mb-5">
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-2">Abstrak</h2>
                    <div class="text-sm text-slate-700 leading-relaxed">
                        <?php echo e($article->submission->abstract); ?>

                    </div>
                </div>
                <?php endif; ?>

                
                <?php if($article->submission->keywords): ?>
                <div class="mb-5 pb-5 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-2">Kata Kunci</h2>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $article->submission->keywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full">
                            <?php echo e($kw); ?>

                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <div class="bg-slate-50 rounded-xl p-4">
                    <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Cara Mengutip</h2>
                    <p class="text-xs text-slate-600 leading-relaxed font-mono">
                        <?php echo e($article->submission->contributors->map(fn($c) => $c->last_name . ', ' . substr($c->first_name, 0, 1) . '.')->join(', ')); ?>

                        (<?php echo e($article->date_published?->format('Y')); ?>).
                        <?php echo e($article->submission->title); ?>.
                        <em><?php echo e($journal->name); ?></em>,
                        <?php if($article->issue): ?> <?php echo e($article->issue->getLabel()); ?>. <?php endif; ?>
                        <?php if($article->doi): ?>https://doi.org/<?php echo e($article->doi); ?><?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        
        <div class="space-y-5">

            
            <?php if(count($galleys) > 0): ?>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Unduh Artikel</h3>
                <div class="space-y-2">
                    <?php $__currentLoopData = $galleys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galley): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($galley['has_file']): ?>
                    <a href="<?php echo e(route('journals.articles.galley', [$journal->slug, $article->id, $galley['id']])); ?>"
                       class="flex items-center gap-3 w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium text-sm transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh <?php echo e($galley['label']); ?></span>
                        <?php if($galley['views'] > 0): ?>
                        <span class="ml-auto opacity-70 text-xs font-normal"><?php echo e(number_format($galley['views'])); ?>×</span>
                        <?php endif; ?>
                    </a>
                    <?php else: ?>
                    <div class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-100 border border-slate-200 border-dashed text-slate-400 text-sm cursor-not-allowed">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span><?php echo e($galley['label']); ?></span>
                        <span class="ml-auto text-xs bg-slate-200 text-slate-500 px-2 py-0.5 rounded-full">Segera Hadir</span>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Informasi Artikel</h3>
                <dl class="space-y-2 text-sm">
                    <?php if($article->date_published): ?>
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">Diterbitkan</dt>
                        <dd class="font-medium text-slate-700"><?php echo e($article->date_published->format('d F Y')); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($article->doi): ?>
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">DOI</dt>
                        <dd>
                            <a href="https://doi.org/<?php echo e($article->doi); ?>" target="_blank" rel="noopener"
                               class="text-blue-600 hover:underline break-all">
                                <?php echo e($article->doi); ?>

                            </a>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if($article->pages): ?>
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">Halaman</dt>
                        <dd class="font-medium text-slate-700"><?php echo e($article->pages); ?></dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">Dilihat</dt>
                        <dd class="font-medium text-slate-700"><?php echo e(number_format($article->views)); ?> kali</dd>
                    </div>
                </dl>
            </div>

            
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Lisensi</h3>
                <div class="flex items-center gap-2">
                    <img src="https://mirrors.creativecommons.org/presskit/icons/cc.svg" alt="CC" class="w-5 h-5">
                    <img src="https://mirrors.creativecommons.org/presskit/icons/by.svg" alt="BY" class="w-5 h-5">
                    <span class="text-xs text-slate-600">CC BY 4.0</span>
                </div>
            </div>
        </div>
    </div>
</div>
