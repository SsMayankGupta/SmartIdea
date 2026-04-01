/**
 * EcoConnect API Client
 * JavaScript wrapper for backend API integration
 */

// Dynamically determine API base URL based on current location
const API_BASE_URL = (() => {
    const path = window.location.pathname;
    // Find the ecoconnect root directory
    if (path.includes('/pages/')) {
        return path.substring(0, path.indexOf('/pages/')) + '/api';
    } else if (path.includes('/auth/')) {
        return path.substring(0, path.indexOf('/auth/')) + '/api';
    } else if (path.includes('/services/')) {
        return path.substring(0, path.indexOf('/services/')) + '/api';
    } else if (path.includes('/education/')) {
        return path.substring(0, path.indexOf('/education/')) + '/api';
    }
    // Default: assume we're at root
    const rootPath = path.substring(0, path.lastIndexOf('/') + 1);
    return rootPath + 'api';
})();

/**
 * Make API request
 */
async function apiRequest(endpoint, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/${endpoint}`, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, error: 'Network error' };
    }
}

/**
 * ================= USER API =================
 */

const UserAPI = {
    // Register new user
    register: async (userData) => {
        return await apiRequest('user.php?action=register', 'POST', userData);
    },
    
    // Login user
    login: async (email, password) => {
        return await apiRequest('user.php?action=login', 'POST', { email, password });
    },
    
    // Get user profile
    getProfile: async (userId) => {
        return await apiRequest(`user.php?action=get_profile&user_id=${userId}`);
    },
    
    // Update profile
    updateProfile: async (userId, profileData) => {
        return await apiRequest('user.php?action=update_profile', 'POST', {
            user_id: userId,
            ...profileData
        });
    },
    
    // Get points history
    getPointsHistory: async (userId, limit = 20) => {
        return await apiRequest(`user.php?action=points_history&user_id=${userId}&limit=${limit}`);
    }
};

/**
 * ================= WASTE REPORT API =================
 */

const ReportAPI = {
    // Submit waste report
    submit: async (userId, reportData) => {
        const data = { user_id: userId, ...reportData };
        return await apiRequest('reports.php?action=submit', 'POST', data);
    },
    
    // Get user's reports
    getMyReports: async (userId, status = null) => {
        let url = `reports.php?action=my_reports&user_id=${userId}`;
        if (status) url += `&status=${status}`;
        return await apiRequest(url);
    },
    
    // Get report statistics
    getStats: async (userId) => {
        return await apiRequest(`reports.php?action=stats&user_id=${userId}`);
    }
};

/**
 * ================= SERVICES API =================
 */

const ServicesAPI = {
    // Get all services
    getServices: async (category = null) => {
        let url = 'services.php?action=list';
        if (category) url += `&category=${category}`;
        return await apiRequest(url);
    },
    
    // Get recycling centers
    getRecyclingCenters: async (city = null, wasteType = null) => {
        let url = 'services.php?action=recycling_centers';
        if (city) url += `&city=${city}`;
        if (wasteType) url += `&waste_type=${wasteType}`;
        return await apiRequest(url);
    }
};

/**
 * ================= QUIZ API =================
 */

const QuizAPI = {
    // Get quiz questions
    getQuestions: async (category = null, difficulty = null, limit = 10) => {
        let url = `quiz.php?action=questions&limit=${limit}`;
        if (category) url += `&category=${category}`;
        if (difficulty) url += `&difficulty=${difficulty}`;
        return await apiRequest(url);
    },
    
    // Submit quiz attempt
    submitAttempt: async (userId, score, totalQuestions, correctAnswers, timeTaken) => {
        return await apiRequest('quiz.php?action=submit', 'POST', {
            user_id: userId,
            score,
            total_questions: totalQuestions,
            correct_answers: correctAnswers,
            time_taken: timeTaken
        });
    }
};

/**
 * ================= EVENTS API =================
 */

const EventsAPI = {
    // Get upcoming events
    getUpcoming: async (city = null, limit = 10) => {
        let url = `events.php?action=upcoming&limit=${limit}`;
        if (city) url += `&city=${city}`;
        return await apiRequest(url);
    },
    
    // Register for event
    register: async (eventId, userId, registrationData) => {
        return await apiRequest('events.php?action=register', 'POST', {
            event_id: eventId,
            user_id: userId,
            ...registrationData
        });
    }
};

/**
 * ================= DASHBOARD API =================
 */

const DashboardAPI = {
    // Get dashboard statistics
    getStats: async (userId) => {
        return await apiRequest(`dashboard.php?action=stats&user_id=${userId}`);
    },
    
    // Get platform impact
    getImpact: async () => {
        return await apiRequest('dashboard.php?action=impact');
    }
};

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { UserAPI, ReportAPI, ServicesAPI, QuizAPI, EventsAPI, DashboardAPI };
}
