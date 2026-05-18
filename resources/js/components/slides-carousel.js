export default (count) => ({
    current: 0,
    count,
    playing: true,
    interval: null,

    next() {
        this.current = (this.current + 1) % this.count
    },
    prev() {
        this.current = (this.current - 1 + this.count) % this.count
    },
    go(i) {
        this.current = i
    },

    start() {
        this.playing = true;
        this.stop();
        this.interval = setInterval(() => this.next(), 5000);
    },

    stop() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
        this.playing = false;
    },

    init() {
        if (this.count > 1) this.start()
    },
});
