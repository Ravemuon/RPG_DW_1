
<?php

// App/Http/Requests/StoreFinalRequest.php

// App/Http/Requests/StoreFinalRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFinalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        // Nenhuma regra de campo é estritamente necessária, pois a submissão
        // apenas aciona a lógica final de salvamento do Controller.
        return [];
    }
}