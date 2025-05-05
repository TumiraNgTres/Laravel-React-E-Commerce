import { Image } from "@/types";
import { useState } from "react";

function Carousel({ images }: { images: Image[] }) {
  const [activeIndex, setActiveIndex] = useState(0);

  // Add a check for empty images array
  if (!images || images.length === 0) {
    return (
      <div className="w-full h-64 bg-gray-200 flex items-center justify-center">
        No images available
      </div>
    );
  }
  return (
    <>
      <div className="flex items-start gap-8">
        <div className="flex flex-col items-center gap-2 py-2">
          {images.map((image, index) => (
            <a
              key={image.id}
              onClick={() => setActiveIndex(index)}
              className={`border-2 ${
                index === activeIndex
                  ? "border-purple-500"
                  : "border-transparent"
              } hover:border-purple-500`}
            >
              <img src={image.thumb} alt="" className="w-[50px]" />
            </a>
          ))}
        </div>

        <div className="carousel w-full">
          {images.map((image, index) => (
            <div key={image.id} className="carousel-item w-full">
              <img
                src={images[activeIndex].large}
                alt=""
                className="w-full max-h-[600px] mx-auto"
              />
            </div>
          ))}
        </div>
      </div>
    </>
  );
}

export default Carousel;
