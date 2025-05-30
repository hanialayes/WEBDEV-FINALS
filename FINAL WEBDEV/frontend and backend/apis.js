document.addEventListener("DOMContentLoaded", () => {
    const genreSlider = document.getElementById("genre-slider");
    const animeContainer = document.getElementById("anime-container");

    // Fetch genres from Jikan API
    fetch("https://api.jikan.moe/v4/genres/anime")
        .then(res => res.json())
        .then(data => {
            var defaultGenre = null;
            data.data.forEach(genre => {
                const btn = document.createElement("button");
                btn.className = "genre-button";
                btn.textContent = genre.name;
                btn.addEventListener("click", () => loadAnimeBy({genres: genre.mal_id}));
                genreSlider.appendChild(btn);
                if (defaultGenre == null) {
                    defaultGenre = genre.mal_id;
                }
            });
            if (defaultGenre != null) {
                loadAnimeBy({genres: defaultGenre});
            }
        });

    // Load anime based on genre
    function loadAnimeBy(filters, limit = 8) {
        animeContainer.innerHTML = "<p>Loading anime...</p>";
        const params = [];
        for (const key in filters) {
            params.push(`${key}=${filters[key]}`);
        }
        fetch(`https://api.jikan.moe/v4/anime?order_by=popularity&limit=${limit}&${params.join('&')}`)
            .then(res => res.json())
            .then(data => {
                animeContainer.innerHTML = "";
                if (data.data.length == 0) {
                    animeContainer.innerHTML = 'No uploaded video.';
                }
                data.data.forEach(anime => {
                    fetchYouTubeVideos(anime.title, true).then(id => {
                        var url = 'https://www.youtube.com/embed/' + id;
                        if (id == null) {
                            url = anime.trailer.url ? anime.trailer.url : anime.trailer.embed_url;
                        }
                        const animeCard = document.createElement("div");
                        animeCard.className = "anime-card";
                        const info = `
                            <h3>${anime.title}</h3>
                            <a href="${url}" target="_blank">
                                <img src="${anime.images.jpg.image_url}" alt="${anime.title}" />
                            </a>
                            <p>${anime.synopsis ? anime.synopsis.substring(0, 200) + "..." : "No description available."}</p>
                        `;
                        animeCard.innerHTML = info;
                        animeContainer.appendChild(animeCard);
                    });
                    // Embed YouTube videos for anime using Muse Asia or Ani-One
                    //fetchYouTubeVideos(anime.title).then(videoHTML => {
                    //    animeCard.innerHTML += videoHTML;
                    //});
                });
            });
    }

    // Fetch YouTube videos (Muse Asia, Ani-One)
    async function fetchYouTubeVideos(animeTitle, idOnly = false) {
        const channels = ["Muse Asia", "Ani-One Asia"];
        const query = encodeURIComponent(`${animeTitle} episode site:youtube.com`);
        const YOUR_API_KEY = '';
        const aniOneAsiaChannel = 'UC0wNSTMWIL3qaorLx0jie6A';
        const params = [
            `key=${YOUR_API_KEY}`,
            "type='video'",
            //order: 'relevance'",
            "part='snippet'",
            "maxResults=1",
            `q=${query}`,
            `channelId=${aniOneAsiaChannel}`,
        ];
        const searchURL = `https://www.googleapis.com/youtube/v3/search?${params.join('&')}`;

        // NOTE: Replace 'YOUR_API_KEY' with your actual API key.
        // Or use server-side proxy to protect the key

        try {
            const res = await fetch(searchURL);
            const data = await res.json();
            if (data.items && data.items.length > 0) {
                const videoId = data.items[0].id.videoId;
                if (idOnly) {
                    return videoId;
                }
                return `
                    <div class="video-wrapper">
                        <iframe width="100%" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>
                    </div>
                `;
            }
        } catch (err) {
            console.error("YouTube Fetch Error:", err);
        }
        if (idOnly) {
            return null;
        }
        return `<p>Episode not available</p>`;
    }

    const elem = document.getElementById('search_anime');
    elem.addEventListener('keypress', (e) => {
        if (e.key == 'Enter') {
            loadAnimeBy({q: elem.value}, 10)
        }
    });
});
