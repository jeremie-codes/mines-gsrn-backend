// API Endpoints and Validation Rules
// MEMBERS Endpoints

members
members/Show (GET)
body = {
    'id' : 'required|exists:members,id',
}

members/Create (POST)
body = {
    'member_id' : 'required|exists:members,id',
    'firstname' : 'nullable|string|max:255',
    'lastname' : 'nullable|string|max:255',
    'middlename' : 'nullable|string|max:255',
    'phone' : 'nullable|string|max:255',
    'site_id' : 'required|exists:sites,id',
    'city_id' : 'nullable|exists:cities,id',
    'township_id' : 'nullable|exists:townships,id',
    'pool_id' : 'nullable|exists:pools,id',
    'libelle_pool' : 'nullable|string|max:255',
    'fonction_id' : 'nullable|exists:fonctions,id',
    'face_image' : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    'face_base64' : 'nullable|string',
    'is_active' : 'boolean|default:true'
}

members/Update (PUT)
body = {
    'member_id' : 'required|exists:members,id',
    'firstname' : 'nullable|string|max:255',
    'lastname' : 'nullable|string|max:255',
    'middlename' : 'nullable|string|max:255',
    'phone' : 'nullable|string|max:255',
    'site_id' : 'required|exists:sites,id',
    'city_id' : 'nullable|exists:cities,id',
    'township_id' : 'nullable|exists:townships,id',
    'pool_id' : 'nullable|exists:pools,id',
    'libelle_pool' : 'nullable|string|max:255',
    'fonction_id' : 'nullable|exists:fonctions,id',
    'face_image' : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    'face_base64' : 'nullable|string',
    'is_active' : 'boolean|default:true'
}

members/Destroy (DELETE)
body = {
    'id' : 'required|exists:members,id',
}

members/{member}/assign-role (POST)
body = {
    'role_id' : 'required|exists:roles,id',
    'member_id' : 'required|exists:members,id',
}

members/{member}/create-user (POST)
body = {
    'email' : 'required|email|max:255|unique:users,email',
    'password' : 'required|string|min:8|confirmed',
    'member_id' : 'required|exists:members,id',
    'role_id' : 'required|exists:roles,id',
}


// API Endpoints and Validation Rules
// SITES Endpoints
sites
sites/Show (GET)
body = {
    'id' : 'required|exists:sites,id',
}

sites/Create (POST)
body = {
    'name' : 'required|string|max:255',
    'code' : 'required|string|max:3|unique:sites,code',
    'location' : 'nullable|string|max:255',
    'is_active' : 'boolean|default:true'
}

sites/Update (PUT)
body = {
    'site_id' : 'required|exists:sites,id',
    'name' : 'required|string|max:255',
    'code' : 'required|string|max:3|unique:sites,code',
    'location' : 'nullable|string|max:255',
    'is_active' : 'boolean|default:true'
}

sites/Destroy (DELETE)
body = {
    'id' : 'required|exists:sites,id',
}

// API Endpoints and Validation Rules
// POOLS Endpoints
pools
pools/Show (GET)
body = {
    'id' : 'required|exists:pools,id',
}

pools/Create (POST)
body = {
    'name' : 'required|string|max:255',
    'description' : 'nullable|string|max:255',
    'site_id' : 'required|exists:sites,id',
    'is_active' : 'boolean|default:true'
}

pools/Update (PUT)
body = {
    'pool_id' : 'required|exists:pools,id',
    'name' : 'required|string|max:255',
    'description' : 'nullable|string|max:255',
    'site_id' : 'required|exists:sites,id',
    'is_active' : 'boolean|default:true'
}

pools/Destroy (DELETE)
body = {
    'id' : 'required|exists:pools,id',
}

// API Endpoints and Validation Rules
// COUNTRIES Endpoints
countries
countries/Show (GET)
body = {
    'id' : 'required|exists:countries,id',
}

countries/Create (POST)
body = {
    'name' : 'required|string|max:255',
    'code' : 'required|string|max:3|unique:countries,code',
    'is_active' : 'boolean|default:true'
}

countries/Update (PUT)
body = {
    'country_id' : 'required|exists:countries,id',
    'name' : 'required|string|max:255',
    'code' : 'required|string|max:3|unique:countries,code',
    'is_active' : 'boolean|default:true'
}

countries/Destroy (DELETE)
body = {
    'id' : 'required|exists:countries,id',
}

// API Endpoints and Validation Rules
// CITIES Endpoints
cities
cities/Show (GET)
body = {
    'id' : 'required|exists:cities,id',
}

cities/Create (POST)
body = {
    'name' : 'required|string|max:255',
    'country_id' : 'required|exists:countries,id',
    'is_active' : 'boolean|default:true'
}

cities/Update (PUT)
body = {
    'city_id' : 'required|exists:cities,id',
    'name' : 'required|string|max:255',
    'country_id' : 'required|exists:countries,id',
    'is_active' : 'boolean|default:true'
}

cities/Destroy (DELETE)
body = {
    'id' : 'required|exists:cities,id',
}

// API Endpoints and Validation Rules
// TOWNSHIPS Endpoints
townships
townships/Show (GET)
body = {
    'id' : 'required|exists:townships,id',
}

townships/Create (POST)
body = {
    'name' : 'required|string|max:255',
    'city_id' : 'required|exists:cities,id',
    'is_active' : 'boolean|default:true'
}

townships/Update (PUT)
body = {
    'township_id' : 'required|exists:townships,id',
    'name' : 'required|string|max:255',
    'city_id' : 'required|exists:cities,id',
    'is_active' : 'boolean|default:true'
}

townships/Destroy (DELETE)
body = {
    'id' : 'required|exists:townships,id',
}

// API Endpoints and Validation Rules
// Functions Endpoints

functions
functions/Show (GET)
body = {
    'id' : 'required|exists:fonctions,id',
}

functions/Create (POST)
body = {
    'name' : 'required|string|max:255',
}

functions/Update (PUT)
body = {
    'fonction_id' : 'required|exists:fonctions,id',
    'name' : 'required|string|max:255',
}

functions/Destroy (DELETE)
body = {
    'id' : 'required|exists:fonctions,id',
}
