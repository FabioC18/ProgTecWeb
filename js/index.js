var elements = document.querySelectorAll('.watch');

var callback = function(items) {
    items.forEach((item) => {
        if (item.isIntersecting) {
            item.target.classList.add("in-page");
        } else {
            item.target.classList.remove("in-page");
        }
    });
}

var observer = new IntersectionObserver(callback, { threshold: 0.5 });
elements.forEach((element) => {
    observer.observe(element);
});


let item = document.querySelector('.hamb-menu');
item.addEventListener("click", function() {
    document.body.classList.toggle('menu-open');
});



class Cont {
    constructor(options) {
        this.el = options.el;
        this.value = options.value;
    }

    update(targetValue) {
        const startValue = this.value;
        const duration = 5000;
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsedTime = currentTime - startTime;
            const progress = Math.min(elapsedTime / duration, 1);


            const currentVal = Math.floor(startValue + (targetValue - startValue) * progress);

            this.el.textContent = currentVal;

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                this.value = targetValue;
            }
        };

        requestAnimationFrame(animate);
    }
}

const createContCl = (el, value) => {
    if (!el) return;

    const cont = new Cont({
        el: el,
        value: 0,
    });

    let hasRun = false;

    const callback = function(items) {
        items.forEach((item) => {
            if (item.isIntersecting) {
                if (!hasRun) {
                    cont.update(value);
                    hasRun = true;

                    observer.unobserve(el);
                }
            }
        });
    };

    var observer = new IntersectionObserver(callback, { threshold: 0.5 });
    observer.observe(el);
};

const contClient = document.querySelector(".cont-client");
createContCl(contClient, 6500);

const contYear = document.querySelector(".cont-year");
createContCl(contYear, 2008);