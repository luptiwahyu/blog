<?php 


$app->get('/change-email', $authenticated(), function() use ($app) {
    $app->render('Pages/EmailChange/email-change.html');
})->name('email_change');


$app->put('/change-email', $authenticated(), function() use ($app) {

    $request = $app->request;

    $email    = filter_var($request->put('email'), FILTER_SANITIZE_EMAIL);
    $password = $request->post('password');

    $v = $app->validation;

    $v->validate([
        'email'    => [$email, 'required|email|uniqueEmail'],
        'password' => [$password, 'required'],
    ]);

    if ($v->passes()) {
        $user = $app->auth;

        if ($user->email && $app->hash->passwordCheck($password, $user->password)) {

            $user->update([
                'email' => $email
            ]);

            $app->flash('success', 'You changed your email.');
            return $app->redirect($app->urlFor('user_profile'));
        }

        $app->flash('error', 'Incorrect password.');
        return $app->redirect($app->urlFor('email_change'));

    }

    $app->render('Pages/EmailChange/email-change.html', [
        'request' => $request,
        'errors'  => $v->errors()->all(),
    ]);

})->name('email_change_post');