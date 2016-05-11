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
import NotFoundPage from '../../../components/app/NotFoundPage';
import { expect } from 'chai';
import { shallow } from 'enzyme';
import { stub } from 'sinon';
import ApiKey from '../../../util/http/ApiKeyService';

describe('NotFoundPage', () => {
  it('renders a 404 page', () => {
    stub(ApiKey, 'isLoggedIn');
    const markup = shallow(<NotFoundPage />);
    expect(markup.find('h1 Translate').prop('content')).to.equal('pages.not_found.title');
    expect(markup.find('.content Translate').prop('content')).to.equal('pages.not_found.text');

    ApiKey.isLoggedIn.restore();
  });
});
