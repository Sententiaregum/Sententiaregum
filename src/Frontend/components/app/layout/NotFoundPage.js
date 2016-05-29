/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React from 'react';
import Translate from 'react-translate-component';

/**
 * Renders the content of the 404 page.
 *
 * @returns {React.Element} The markup.
 */
const NotFoundPage = () => {
  return (
    <div>
      <h1>
        <Translate content="pages.not_found.title" />
      </h1>
      <div className="content">
        <Translate content="pages.not_found.text" />
      </div>
    </div>
  );
};

export default NotFoundPage;
