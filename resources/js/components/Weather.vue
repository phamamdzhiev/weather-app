<template>
    <div class="weather-container">
        <h1>Weather Data</h1>

        <div class="input-group">
            <input v-model="city" type="search" placeholder="Enter city name" @keyup.enter="fetchWeatherData" />
            <button @click="fetchWeatherData">Get Weather</button>
        </div>

        <div v-if="loading" class="loading-message">
            Please wait...
        </div>

        <div v-if="weatherData">
            <table>
                <thead>
                    <tr>
                        <th>City Name</th>
                        <th>Temperature</th>
                        <th>Weather Description</th>
                        <th>Humidity</th>
                        <th>Wind Speed</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ weatherData.name }}</td>
                        <td>{{ Math.round(weatherData.main.temp) }}°C</td>
                        <td>{{ weatherData.weather[0]?.description }}</td>
                        <td>{{ weatherData.main.humidity }}%</td>
                        <td>{{ Math.round(weatherData.wind.speed).toFixed(1) }} m/s</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="error && !loading" class="error">
            {{ error }}
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            city: '',
            weatherData: null,
            error: null,
            loading: false
        };
    },
    methods: {
        async fetchWeatherData() {
            if (!this.city.trim()) {
                this.error = 'City name cannot be empty';
                this.weatherData = null;
                return;
            }

            this.error = null;
            this.loading = true;

            try {
                const response = await axios.get(`http://localhost:8000/api/weather`, {
                    params: {
                        city: this.city,
                    },
                });

                this.weatherData = response.data;
            } catch (err) {
                this.weatherData = null;
                this.error = err?.response?.data?.error ?? err?.message
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.weather-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}

.loading-message {
    font-size: 18px;
    color: #555;
    margin-top: 20px;
}

.input-group {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

input {
    padding: 10px;
    font-size: 16px;
    width: 70%;
}

button {
    padding: 10px 15px;
    font-size: 16px;
    margin-left: 10px;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th,
td {
    padding: 10px;
    border: 1px solid #ddd;
}

th {
    background-color: #f4f4f4;
}

.error {
    color: red;
    margin-top: 20px;
}
</style>