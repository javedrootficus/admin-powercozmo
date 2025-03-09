<?php
namespace App\Models;
// use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'content'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}