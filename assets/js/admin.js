document.addEventListener('DOMContentLoaded', function()
{
    tocbot.init({
        tocSelector: '.wordplan-toc',
        contentSelector: '.wordplan-toc-container',
        headingSelector: 'h1, h2, h3, h4, h5',
        hasInnerContainers: true,
        listClass: 'list-group m-0',
        linkClass: 'stretched-link',
        listItemClass: 'list-group-item',
        activeListItemClass: 'active',
        headingsOffset: 55,
        scrollSmoothOffset: -55,
        positionFixedSelector: '.wordplan-sidebar-toc',
        positionFixedClass: 'is-position-fixed position-sticky',
    });
});