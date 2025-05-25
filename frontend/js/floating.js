document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector("#socialize-inline-container");
    const socializeFloating = document.querySelectorAll('.socialize-floating');
    if (socializeFloating.length > 0) {
        var iw = document.body.clientWidth;
        if (iw > 400) {
            socializeFloating.forEach(function (element) {
                element.style.position = 'absolute';
                setTimeout(function () {
                    element.style.opacity = 0;
                    element.style.transition = 'opacity 600ms';
                    setTimeout(function () {
                        element.style.opacity = 1;
                    }, 0);
                }, 1000);
            });
        }

        socializeScroll();
        socializeResize();
        window.addEventListener("resize", socializeResize);
        window.addEventListener("scroll", socializeScroll);
    }

    function socializeScroll() {
        const barContainer = document.getElementById('socialize-inline-container');
        const bar = document.querySelector('.socialize-floating ');
        if (!barContainer || !barContainer.parentElement) return;

        const content = barContainer.parentElement;
        const barHeight = document.querySelector('.socialize-floating ').offsetHeight;

        const scrollY = window.scrollY || window.pageYOffset;
        const contentTop = content.offsetTop;
        const contentBottom = contentTop + content.offsetHeight;
        const maxScroll = contentBottom - barHeight;

        if (scrollY > contentTop && scrollY < maxScroll) {
            bar.style.position = 'fixed';
            bar.style.top = '30px'; // Added 30px padding to the top
        } else if (scrollY >= maxScroll) {
            bar.style.position = 'absolute';
            bar.style.top = (content.offsetHeight - barHeight) + 'px';
        } else {
            bar.style.position = 'absolute';
            bar.style.top = '0px';
        }
    }

    function socializeResize() {
        const socializeFloating = document.querySelector('.socialize-floating');
        if (socializeFloating) {
            var rect = socializeFloating.getBoundingClientRect();
            var l = rect.left;
            var w = socializeFloating.offsetWidth;
            if(w == 0) {
                w = 85;
            }
            var docW = window.innerWidth;
            var isEntirelyVisible = (l > 0 && (l + w) < docW);
            if (isEntirelyVisible) {
                socializeFloating.style.display = 'flex';
            } else {
                socializeFloating.style.display = 'none';
            }
        }
    }
});
