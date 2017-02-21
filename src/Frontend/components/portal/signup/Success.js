/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React               from 'react';
import DismissableAlertBox from '../../app/markup/DismissableAlertBox';
import Translate           from 'react-translate-component';

/**
 * Rendering component of the success box.
 *
 * @returns {React.Element} The markup.
 */
const Success = () => {
  return (
    <DismissableAlertBox bsStyle="success">
      <p><Translate content="pages.portal.create_account.success" /></p>
    </DismissableAlertBox>
  );
};

export default Success;
