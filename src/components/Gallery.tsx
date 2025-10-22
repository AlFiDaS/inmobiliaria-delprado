import { useState, useEffect } from 'react';
import type { Property } from '@/data/properties';

interface GalleryProps {
  property: Property;
}

export default function Gallery({ property }: GalleryProps) {
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [activeTab, setActiveTab] = useState<'images' | 'videos'>('images');
  const { images, videos } = property;

  const hasVideos = videos && videos.length > 0;

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        goToPrevious();
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        goToNext();
      }
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, [currentImageIndex, images.length]);

  const goToPrevious = () => {
    setCurrentImageIndex(prev => 
      prev === 0 ? images.length - 1 : prev - 1
    );
  };

  const goToNext = () => {
    setCurrentImageIndex(prev => 
      prev === images.length - 1 ? 0 : prev + 1
    );
  };

  const goToImage = (index: number) => {
    setCurrentImageIndex(index);
  };

  const renderVideo = (video: { kind: 'file' | 'youtube' | 'vimeo'; src: string }) => {
    if (video.kind === 'youtube') {
      const videoId = video.src.split('/').pop()?.split('?')[0];
      return (
        <iframe
          src={`https://www.youtube.com/embed/${videoId}`}
          title="Video de la propiedad"
          className="w-full h-full"
          allowFullScreen
        />
      );
    } else if (video.kind === 'vimeo') {
      const videoId = video.src.split('/').pop();
      return (
        <iframe
          src={`https://player.vimeo.com/video/${videoId}`}
          title="Video de la propiedad"
          className="w-full h-full"
          allowFullScreen
        />
      );
    } else {
      return (
        <video
          src={video.src}
          controls
          className="w-full h-full"
          preload="metadata"
        >
          Tu navegador no soporta el elemento de video.
        </video>
      );
    }
  };

  return (
    <div className="gallery-container">
      {/* Tabs */}
      {hasVideos && (
        <div className="flex mb-4">
          <button
            onClick={() => setActiveTab('images')}
            className={`px-4 py-2 text-sm font-medium rounded-l-md border ${
              activeTab === 'images'
                ? 'bg-orange-600 text-white border-orange-600'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            }`}
          >
            Imágenes ({images.length})
          </button>
          <button
            onClick={() => setActiveTab('videos')}
            className={`px-4 py-2 text-sm font-medium rounded-r-md border-t border-r border-b ${
              activeTab === 'videos'
                ? 'bg-orange-600 text-white border-orange-600'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            }`}
          >
            Videos ({videos.length})
          </button>
        </div>
      )}

      {/* Contenido principal */}
      <div className="relative bg-gray-100 rounded-lg overflow-hidden aspect-4-3">
        {activeTab === 'images' ? (
          <>
            {/* Imagen principal */}
            <img
              src={images[currentImageIndex]}
              alt={`${property.title} - Imagen ${currentImageIndex + 1}`}
              className="w-full h-full object-cover"
            />

            {/* Navegación */}
            {images.length > 1 && (
              <>
                <button
                  onClick={goToPrevious}
                  className="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity"
                  aria-label="Imagen anterior"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <button
                  onClick={goToNext}
                  className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity"
                  aria-label="Imagen siguiente"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </>
            )}

            {/* Contador */}
            {images.length > 1 && (
              <div className="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-sm">
                {currentImageIndex + 1} / {images.length}
              </div>
            )}
          </>
        ) : (
          /* Videos */
          <div className="w-full h-full">
            {videos && videos[0] && renderVideo(videos[0])}
          </div>
        )}
      </div>

      {/* Thumbnails */}
      {activeTab === 'images' && images.length > 1 && (
        <div className="mt-4 flex space-x-2 overflow-x-auto scrollbar-hide">
          {images.map((image, index) => (
            <button
              key={index}
              onClick={() => goToImage(index)}
              className={`gallery-thumbnail flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 ${
                index === currentImageIndex
                  ? 'border-primary-500'
                  : 'border-gray-200 hover:border-gray-300'
              }`}
            >
              <img
                src={image}
                alt={`Miniatura ${index + 1}`}
                className="w-full h-full object-cover"
              />
            </button>
          ))}
        </div>
      )}

      {/* Instrucciones de teclado */}
      {activeTab === 'images' && images.length > 1 && (
        <p className="mt-2 text-xs text-gray-500 text-center">
          Usa las flechas ← → del teclado para navegar
        </p>
      )}
    </div>
  );
}
