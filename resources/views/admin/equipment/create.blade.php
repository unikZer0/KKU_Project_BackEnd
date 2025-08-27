<x-admin-layout>
<div class="page-content">
    <div class="page-header">
    <div class="container-fluid">
        <a1>เพิ่มอุปกรณ์</a1>
        <div class="div_deg">
            <form action="{{  route("admin.equipment.store") }}" method="post"
            enctype="multipart/form-data">
            @csrf
                <div class="input-deg">
                    <label for="">ชื่ออุปกรณ์</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
                <div class="input-deg">
                    <label for="">รายละเอียดอุปกรณ์</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <div class="input_deg">
                    <label>Product category</label>
                    <select name="categories_id" class="form-control" required>
                        <option>Select a option</option>
                        @foreach ($categories as $category) 
                            <option value="{{ $category->id }}">
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input_deg">
                    <label for="status">สถานะอุปกรณ์</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- เลือกสถานะ --</option>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

               <div class="input_deg">
    <label>Product image</label>
    <input type="file" name="photo_path" id="photo_path" accept="image/*" onchange="previewImage(event)">
    <br>
    <img id="image_preview" src="#" alt="Preview" style="display: none; max-width: 200px; margin-top: 10px;">
</div>


                <div class="input_deg">
                    <input class="btn btn-success" type="submit" value="เพิ่มอุปกรณ์">
                </div>
            </form>
        </div>
    </div>
<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('image_preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
}
</script>
</x-admin-layout>