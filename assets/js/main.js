class ProgressiveImageLoader {
    constructor(imageId, imageUrls, delay = 800, initialDelay = 400) {
        this.imageElement = document.getElementById(imageId);
        this.imageUrls = imageUrls;
        this.delay = delay;
        this.initialDelay = initialDelay;
        this.currentStage = 0;

        if (this.imageElement) {
            setTimeout(() => this.loadNextStage(), this.initialDelay);
        }
    }

    loadNextStage() {
        if (this.currentStage >= this.imageUrls.length) return;

        const img = new Image();
        img.onload = () => {
            this.imageElement.src = this.imageUrls[this.currentStage];

            if (this.currentStage === 0) {
                this.imageElement.style.filter = 'blur(8px)';
            } else {
                this.imageElement.style.filter = 'blur(0px)';
            }

            this.currentStage++;

            if (this.currentStage < this.imageUrls.length) {
                setTimeout(() => this.loadNextStage(), this.delay);
            }
        };

        img.src = this.imageUrls[this.currentStage];
    }
}